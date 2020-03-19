<?php

/***************************************************************
 Copyright (C) 2020 HH Partners, Attorneys at Law
 Author: Mikko Murto <mikko.murto@hhpartners.fi>

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ***************************************************************/
/**
 * @file
 * @brief Controller for license conclusion queries
 */

namespace Fossology\UI\Api\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Fossology\UI\Api\Models\Info;
use Fossology\UI\Api\Models\InfoType;
use Fossology\UI\Api\Models\SearchResult;

/**
 * @class ConclusionController
 * @brief Controller for Conclusion model
 */
class ConclusionController extends RestController
{
    /**
     * Perform a search for concluded licenses by their hash values
     */
    public function getConclusions($request, $response, $args)
    {
        $hashListJSON = $request->getParsedBody();
        if (empty($hashListJSON)) {
            $error = new Info(403, "No hash list provided!", InfoType::ERROR);
            return $response->withJson($error->getArray(), $error->getCode());
        }

        if ($request->hasHeader('userId') && is_numeric($userId = $request->getHeaderLine('userId')) && $userId > 0) {
            $userId = $request->getHeaderLine('userId');
        } elseif($userId == 'self') {
            $userId = $this->restHelper->getUserId();
        } else {
            $userId = 0;
        }

        $hashListWithConclusions = [];
        for ($i = 0; $i < sizeof($hashListJSON); $i ++) {
            $hashListWithConclusions[] = array($hashListJSON[$i] => $this->dbHelper->getConcludedLicenses($hashListJSON[$i], $userId));
        }
        return $response->withJson($hashListWithConclusions, 200);
    }
}
