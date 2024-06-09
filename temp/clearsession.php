<?php

session_start(); // Start de sessie
session_destroy(); // Verwijder de sessie

// Ga terug naar /index.php
header("location:/");