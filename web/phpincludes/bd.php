<?php

function bd_connect() {
        mysql_pconnect("HOST", "USER", "PASSWORD");
        mysql_select_db("DATABASE");
}
