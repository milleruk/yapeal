<?php
/**
 * Contains AbstractAccountSection class.
 *
 * PHP version 5.4
 *
 * LICENSE:
 * This file is part of Yet Another Php Eve Api Library also know as Yapeal
 * which can be used to access the Eve Online API data and place it into a
 * database.
 * Copyright (C) 2014-2015 Michael Cummings
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * You should be able to find a copy of this license in the LICENSE.md file. A
 * copy of the GNU GPL should also be available in the GNU-GPL.md file.
 *
 * @copyright 2014-2015 Michael Cummings
 * @license   http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */
namespace Yapeal\Database\Account;

use LogicException;
use PDO;
use PDOException;
use Yapeal\Database\AbstractCommonEveApi;
use Yapeal\Database\EveSectionNameTrait;
use Yapeal\Xml\EveApiPreserverInterface;
use Yapeal\Xml\EveApiReadWriteInterface;
use Yapeal\Xml\EveApiRetrieverInterface;

/**
 * Class AbstractAccountSection
 */
abstract class AbstractAccountSection extends AbstractCommonEveApi
{
    use EveSectionNameTrait;
    /**
     * @param EveApiReadWriteInterface $data
     * @param EveApiRetrieverInterface $retrievers
     * @param EveApiPreserverInterface $preservers
     * @param int                      $interval
     *
     * @throws LogicException
     */
    public function autoMagic(
        EveApiReadWriteInterface $data,
        EveApiRetrieverInterface $retrievers,
        EveApiPreserverInterface $preservers,
        $interval
    ) {
        $this->getLogger()
             ->debug(
                 sprintf(
                     'Starting autoMagic for %1$s/%2$s',
                     $this->getSectionName(),
                     $this->getApiName()
                 )
             );
        $active = $this->getActiveKeys();
        if (empty($active)) {
            $this->getLogger()
                 ->info('No active registered keys found');
            return;
        }
        foreach ($active as $key) {
            $data->setEveApiSectionName(strtolower($this->getSectionName()))
                 ->setEveApiName($this->getApiName());
            if ($this->cacheNotExpired(
                $this->getApiName(),
                $this->getSectionName(),
                $key['keyID']
            )
            ) {
                continue;
            }
            $data->setEveApiArguments($key)
                 ->setEveApiXml();
            $untilInterval = $interval;
            if (!$this->oneShot(
                $data,
                $retrievers,
                $preservers,
                $untilInterval
            )
            ) {
                continue;
            }
            $this->updateCachedUntil(
                $data->getEveApiXml(),
                $untilInterval,
                $key['keyID']
            );
        }
    }
    /**
     * @param EveApiReadWriteInterface $data
     * @param EveApiRetrieverInterface $retrievers
     * @param EveApiPreserverInterface $preservers
     * @param int                      $interval
     *
     * @throws LogicException
     * @return bool
     */
    public function oneShot(
        EveApiReadWriteInterface &$data,
        EveApiRetrieverInterface $retrievers,
        EveApiPreserverInterface $preservers,
        &$interval
    ) {
        if (!$this->gotApiLock($data)) {
            return false;
        }
        $keyID = $data->getEveApiArguments();
        $keyID = $keyID['keyID'];
        /**
         * @type EveApiReadWriteInterface $data
         */
        $retrievers->retrieveEveApi($data);
        if ($data->getEveApiXml() === false) {
            $mess = sprintf(
                'Could NOT retrieve any data from Eve API %1$s/%2$s for %3$s',
                strtolower($this->getSectionName()),
                $this->getApiName(),
                $keyID
            );
            $this->getLogger()
                 ->notice($mess);
            return false;
        }
        $this->xsltTransform($data);
        if ($this->isInvalid($data)) {
            $mess = sprintf(
                'The data retrieved from Eve API %1$s/%2$s for %3$s is invalid',
                strtolower($this->getSectionName()),
                $this->getApiName(),
                $keyID
            );
            $this->getLogger()
                 ->warning($mess);
            $data->setEveApiName('Invalid' . $this->getApiName());
            $preservers->preserveEveApi($data);
            return false;
        }
        $preservers->preserveEveApi($data);
        // No need / way to preserve XML errors to the database with normal
        // preserve.
        if ($this->isEveApiXmlError($data, $interval)) {
            return true;
        }
        $this->preserve(
            $data->getEveApiXml(),
            $keyID
        );
        return true;
    }
    /**
     * @param string $xml
     * @param string $ownerID
     *
     * @return self
     */
    abstract protected function preserve($xml, $ownerID);
    /**
     * @throws LogicException
     * @return array
     */
    protected function getActiveKeys()
    {
        $sql = $this->getCsq()
                    ->getActiveRegisteredKeys();
        $this->getLogger()
             ->debug($sql);
        try {
            $stmt = $this->getPdo()
                         ->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exc) {
            $mess = 'Could NOT select from utilRegisteredKeys';
            $this->getLogger()
                 ->warning($mess, ['exception' => $exc]);
            return [];
        }
    }
}
