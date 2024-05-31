<?php

function foutmelding(int $id)
{
  header("location: ./?error=$id");
}