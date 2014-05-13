<?php
/**
 * Contains Section Server class.
 *
 * PHP version 5
 *
 * LICENSE:
 * This file is part of Yet Another Php Eve Api Library also know as Yapeal which can be used to access the Eve Online
 * API data and place it into a database.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author     Michael Cummings <mgcummings@yahoo.com>
 * @copyright  Copyright (c) 2008-2014, Michael Cummings
 * @license    http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @link       http://code.google.com/p/yapeal/
 * @link       http://www.eveonline.com/
 */
namespace Yapeal\Database\Section;

use Psr\Log\LoggerInterface;
use Yapeal\Database\AbstractApiRequest;
use Yapeal\Database\AbstractSection;
use Yapeal\Database\DBConnection;
use Yapeal\Database\Util\AccessMask;
use Yapeal\Database\Util\CachedUntil;

/**
 * Class used to pull Eve APIs for server section.
 */
class Server extends AbstractSection
{
    /**
     * Constructor
     *
     * @param AccessMask|null $accessMask
     * @param int             $activeAPIMask
     * @param LoggerInterface $logger
     */
    public function __construct(
        AccessMask $accessMask = null,
        $activeAPIMask,
        LoggerInterface $logger
    ) {
        $this->section =
            strtolower(basename(str_replace('\\', '/', __CLASS__)));
        parent::__construct($accessMask, $activeAPIMask, $logger);
    }
    /**
     * Function called by Yapeal.php to start section pulling XML from servers.
     *
     * @return bool Returns TRUE if all APIs were pulled cleanly else FALSE.
     */
    public function pullXML()
    {
        $apiCount = 0;
        $apiSuccess = 0;
        $apis = $this->accessMask->maskToAPIs($this->mask, $this->section);
        if (count($apis) == 0) {
            return false;
        }
        // Randomize order in which APIs are tried if there is a list.
        if (count($apis) > 1) {
            shuffle($apis);
        }
        try {
            foreach ($apis as $api) {
                // See if Yapeal has been running for longer than 'soft' limit.
                if (YAPEAL_MAX_EXECUTE < time()) {
                    if (\Logger::getLogger('yapeal')
                               ->isInfoEnabled()
                    ) {
                        $mess =
                            'Yapeal has been working very hard and needs a break';
                        \Logger::getLogger('yapeal')
                               ->info($mess);
                    }
                    exit;
                }
                $class =
                    '\\Yapeal\\Database\\' . ucfirst($this->section) . '\\'
                    . $api;
                if (!class_exists($class)) {
                    continue;
                }
                // If the cache has NOT expired there is nothing to do.
                if (CachedUntil::cacheExpired($api) === false
                ) {
                    continue;
                }
                ++$apiCount;
                $hash = hash('sha1', $class);
                // These are passed on to the API class instance and used as part of
                // hash for lock.
                $params = array();
                // Use lock to keep from wasting time trying to do API that another
                // Yapeal is already working on.
                try {
                    $con = DBConnection::connect(YAPEAL_DSN);
                    $sql = 'select get_lock(' . $con->qstr($hash) . ',5)';
                    if ($con->GetOne($sql) != 1) {
                        if (\Logger::getLogger('yapeal')
                                   ->isInfoEnabled()
                        ) {
                            $mess =
                                'Failed to get lock for ' . $class . $hash;
                            \Logger::getLogger('yapeal')
                                   ->info($mess);
                        }
                        continue;
                    }
                } catch (\ADODB_Exception $e) {
                    continue;
                }
                // Give each API 60 seconds to finish. This should never happen but is
                // here to catch runaways.
                set_time_limit(60);
                /**
                 * @var AbstractApiRequest $instance
                 */
                $instance = new $class($params);
                if ($instance->apiStore()) {
                    ++$apiSuccess;
                }
                $instance = null;
            }
        } catch (\ADODB_Exception $e) {
            \Logger::getLogger('yapeal')
                   ->warn($e);
        }
        // Only truly successful if API was fetched and stored.
        if ($apiCount == $apiSuccess) {
            return true;
        } else {
            return false;
        }
    }
}

