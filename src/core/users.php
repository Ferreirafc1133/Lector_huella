<?php
/**
 * This file is used to display user names.
 */

namespace fingerprint;

require("querydb.php");
require_once("helpers/helpers.php");

header('Content-Type: application/json');
echo getAllUsernames();
