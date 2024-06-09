<?php

var_dump($_SERVER["REQUEST_URI"]);
echo "<hr/>";
var_dump(parse_url($_SERVER["REQUEST_URI"]));