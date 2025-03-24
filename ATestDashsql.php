<?php
    include_once('db_connecter.php');

    $avgPeak = "SELECT 
            DAYNAME(StartDateTime) AS DayOfWeek,
            HOUR(StartDateTime) AS HourOfDay,
            COUNT(*) AS SessionCount
            FROM 
                elitewmzsu_db7.GameSession
            WHERE 
                StartDateTime IS NOT NULL
            GROUP BY 
                DayOfWeek, HourOfDay
            ORDER BY 
                FIELD(DAYNAME(StartDateTime), 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
                SessionCount DESC";

    return $sql;
?>