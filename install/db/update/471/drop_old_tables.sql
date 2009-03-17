/**
 * MySQL file.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Yet Another Php Eve Api library also know
 * as Yapeal which will be used to refer to it in the rest of this license.
 *
 *  Yapeal is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Yapeal is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with Yapeal. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Michael Cummings <mgcummings@yahoo.com>
 * @copyright Copyright (c) 2008-2009, Michael Cummings
 * @license http://www.gnu.org/copyleft/lesser.html GNU LGPL
 * @package Yapeal
 */
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP TABLE IF EXISTS `AccountBalance`;
DROP TABLE IF EXISTS `AllianceList`;
DROP TABLE IF EXISTS `AssetList`;
DROP TABLE IF EXISTS `CachedUntil`;
DROP TABLE IF EXISTS `certificates`;
DROP TABLE IF EXISTS `CharacterSheet`;
DROP TABLE IF EXISTS `ConquerableStationList`;
DROP TABLE IF EXISTS `ContainerLog`;
DROP TABLE IF EXISTS `CorporationSheet`;
DROP TABLE IF EXISTS `divisions`;
DROP TABLE IF EXISTS `ErrorList`;
DROP TABLE IF EXISTS `IndustryJobs`;
DROP TABLE IF EXISTS `logo`;
DROP TABLE IF EXISTS `MarketOrders`;
DROP TABLE IF EXISTS `MemberTracking`;
DROP TABLE IF EXISTS `RefTypes`;
DROP TABLE IF EXISTS `RegisteredCharacter`;
DROP TABLE IF EXISTS `RegisteredCorporation`;
DROP TABLE IF EXISTS `RegisteredUser`;
DROP TABLE IF EXISTS `ServerStatus`;
DROP TABLE IF EXISTS `skills`;
DROP TABLE IF EXISTS `StarbaseList`;
DROP TABLE IF EXISTS `walletDivisions`;
DROP TABLE IF EXISTS `WalletJournal`;
DROP TABLE IF EXISTS `WalletTransactions`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;