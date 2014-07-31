<?php
/**
 * Contains AccountBalance class.
 *
 * PHP version 5.3
 *
 * LICENSE:
 * This file is part of 1.1.x-WIP
 * Copyright (C) 2014 Michael Cummings
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * You should be able to find a copy of this license in the LICENSE.md file. A copy of the GNU GPL should also be
 * available in the GNU-GPL.md file.
 *
 * @copyright 2014 Michael Cummings
 * @license   http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */
namespace Yapeal\Database\Corp;

use Yapeal\Database\AbstractAccountKey;
use Yapeal\Database\AttributesDatabasePreserverTrait;
use Yapeal\Database\EveApiNameTrait;
use Yapeal\Database\EveSectionNameTrait;

/**
 * Class AccountBalance
 */
class AccountBalance extends AbstractAccountKey
{
    use EveApiNameTrait, EveSectionNameTrait, AttributesDatabasePreserverTrait;
    /**
     * @param string $xml
     * @param string $ownerID
     * @param string $accountKey
     *
     * @return self
     */
    protected function preserverToAccountBalance($xml, $ownerID, $accountKey)
    {
        $columnDefaults = array(
            'ownerID' => $ownerID,
            'accountID' => null,
            'accountKey' => $accountKey,
            'balance' => null
        );
        $this->attributePreserveData(
            $xml,
            $columnDefaults,
            'corpAccountBalance'
        );
        return $this;
    }
    /**
     * @var int $mask
     */
    protected $mask = 1;
    /**
     * @var int $maxKeyRange
     */
    protected $maxKeyRange = 1006;
}