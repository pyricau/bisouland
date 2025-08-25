<?php

include __DIR__.'/../config/parameters.php';

function bd_connect()
{
    mysql_pconnect(
        DATABASE_HOST.':'.DATABASE_PORT,
        DATABASE_USER,
        DATABASE_PASSWORD
    );
    mysql_select_db(DATABASE_NAME);
}
