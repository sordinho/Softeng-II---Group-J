<?php
require_once('config.php');
require_once('functions.php');
require_once('user.php');
/**
 *
 * @param $service_name
 * @return array|bool
 */

// Queue ticket handler
/**
 * Richiede l'uso di dummy ticket
 * @param $service_name
 * @return array struttura ticket info
 */
function add_top($service_name) {
    // Do an insert and get back the info about the generated number
    $mysqli = connectMySQL();
    $sql = 'INSERT INTO Queue(ServiceID, TicketNumber) SELECT Service.ID,MAX(TicketNumber)+1  FROM Queue JOIN Service ON ServiceID=Service.ID WHERE Service.Name = ? GROUP BY ServiceID';
    $query = $mysqli->prepare($sql);
    //echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    $query->bind_param('s', $service_name);
    $res = $query->execute();
    $last_id = $mysqli->insert_id;
    //print($last_id);
    if (!$res) {
        printf("Error message: %s\n", $mysqli->error);
        return false;
    } else {
        $query->close();
        //die();
        $sql = "SELECT * FROM Queue WHERE ID = $last_id";
        $ticket_info = array();
        if ($result = $mysqli->query($sql)) {
            /* fetch object array */
            $row = $result->fetch_object();
            $ticket_info['ID'] = $row->ID;
            $ticket_info['serviceID'] = $row->ServiceID;
            $ticket_info['ticketN'] = $row->TicketNumber;
            $ticket_info['timestamp'] = $row->Timestamp;
            $result->close();
            $mysqli->close();
        }
        return $ticket_info;
    }
}

// Add a dummy ticket. This will be called when, for a given service, there are no more records in the Queue table.
/**
 * Add a dummy ticket. This will be called when, for a given service, there are no more records in the Queue table.
 * E' un biglietto fittizio finalizzato alla inizializzazione
 * deve essere automaticamente richiamato nel caso in cui la coda è vuota
 * e viene eliminato quando viene servito il primo utente reale
 *
 * Il valore 0 è un valore che non corrisponde a nessun ticket
 * @param $service_id
 * @return array struttura ticket info
 */
function add_dummy_ticket($service_id) {
    // Do an insert and get back the info about the generated number
    $mysqli = connectMySQL();
    $sql = 'INSERT INTO Queue(ServiceID, TicketNumber) VALUES (?, 0)';
    $query = $mysqli->prepare($sql);
    //echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    $query->bind_param('d', $service_id);
    $res = $query->execute();
    $last_id = $mysqli->insert_id;
    //print($last_id);
    if (!$res) {
        printf("Error message: %s\n", $mysqli->error);
        return false;
    } else {
        $query->close();
        //die();
        $sql = "SELECT * FROM Queue WHERE ID = $last_id";
        $ticket_info = array();
        if ($result = $mysqli->query($sql)) {
            /* fetch object array */
            $row = $result->fetch_object();
            $ticket_info['ID'] = $row->ID;
            $ticket_info['serviceID'] = $row->ServiceID;
            $ticket_info['ticketN'] = $row->TicketNumber;
            $ticket_info['timestamp'] = $row->Timestamp;
            $result->close();
            $mysqli->close();
        }
        return $ticket_info;
    }
}

/**
 * @param $service_name
 * @return ticket number più basso
 */
function get_bottom($service_name) {
    $conn = connectMySQL();
    $sql = "SELECT MIN(TicketNumber) as TicketN FROM Queue JOIN Service ON ServiceID = Service.ID WHERE Service.Name = '$service_name'";
    if ($result = $conn->query($sql)) {
        /* fetch object array */
        $row = $result->fetch_object();
        $ticketN = $row->TicketN;

        $result->close();
        return $ticketN;
    } else {
        printf("Error message: %s\n", $conn->error);
    }
}

/**
 * Se il service ID = 0 => serve tutti i servizi
 * > Calcola la coda con più gente in coda
 * > Nel caso di due code con la stessa lunghezza sceglie quelli
 *   con il ticket di valore minore e si ordina in base al timestamp e di questi si prende quelli con timestamp più antico
 * > Il caso della non esistenza delle code non è gestito per la presenza del dummy ticket
 * Se il serviceID != -1 (il -1 rappresenta l'admin)
 *
 *
 * il controllo sul -1 è una doppia sicurezza
 * @param $serviceID
 * @return array|null
 */
function get_next($serviceID) {

    if ($serviceID == 0) {
        /*
         * get the current minimum ticket number from the current most sized queue
         * in case of equal queue size, the query picks the ticket number of the serviceID queue
         * with the minimum timestamp i.e. higher waiting time
         */

        /*
         *  select max(s.count) maximum from (select count(*) count from Queue group by ServiceID) s;
            select serviceID from Queue group by ServiceID having count(*)=2;
            select serviceID, min(ticketNumber) ticketN from Queue where serviceID in (select serviceID from Queue group by ServiceID having count(*)=2) group by serviceID;
            select id, serviceID, ticketNumber ticketN, timestamp from Queue where ticketNumber in (select min(ticketNumber) from Queue where serviceID in (select serviceID from Queue group by ServiceID having count(*)=2) group by serviceID) order by timestamp asc limit 1;
         */
        $conn = connectMySQL();
        /*
         * first it gets the maximum sized queue and stores the result into a variable
         */
        $query1 = "select max(s.count) maximum from (select count(*) count from Queue group by ServiceID) s";
        $ticket_info = array();
        if ($result1 = $conn->query($query1)) {
            $row = $result1->fetch_object();
            $count = $row->maximum;
            /*
             * even if there's more than one queue the result is limited by 1 so I always get the ticketN
             * from the maximum sized queue ordered by timestamps
             * given the maximum count the query search the minimum numbered ticket
             * from a partial window that displays all the serviceID queues given $count
             * and in theper end it limits the result at 1 to only get a single ticket
             */
            $query2 = "SELECT ID,ServiceID AS serviceID, MIN(ticketNumber) AS ticketN,Timestamp AS timestamp
                        FROM Queue
                        WHERE ServiceID in (SELECT ServiceID
                                            FROM Queue
                                            GROUP BY ServiceID
                                            HAVING COUNT(*) = $count)
                        GROUP BY ServiceID
                        ORDER BY Timestamp ASC LIMIT 1";
            if ($result2 = $conn->query($query2)) {
                if ($result2->num_rows === 1) {
                    $ticket_info = $result2->fetch_assoc();
                }
            } else {
                printf("Error message: %s\n", $conn->error);
                print("\n" . $serviceID);
                die($query2);
            }
        } else {
            printf("Error message: %s\n", $conn->error);
            print("\n" . $serviceID);
            die($query1);
        }

        return $ticket_info;
    } elseif ($serviceID != -1) {
        /*
         * get the minimum numbered ticket from a given serviceID queue
         */
        $conn = connectMySQL();
        $sql = "SELECT ID, ServiceID AS serviceID, TicketNumber ticketN, Timestamp AS timestamp from Queue where TicketNumber IN (SELECT MIN(TicketNumber) FROM Queue WHERE ServiceID=$serviceID) AND ServiceID=$serviceID";
        $ticket_info = array();
        if ($result = $conn->query($sql)) {
            if ($conn->affected_rows === 1) {
                $ticket_info = $result->fetch_assoc();
            }
        } else {
            printf("Error message: %s\n", $conn->error);
            print("\n" . $serviceID);
            die($sql);
        }
        return $ticket_info;
    }

}

/**
 * Elimina il ticket
 * @param $serviceID
 * @param $ticketN
 * @return true or false se avviene o meno
 */
function delete_ticket($serviceID, $ticketN) {
    /*
     * delete one ticket for the specified serviceID, ticketnum
     * if one row has been affected returns true
     * false instead
     */
    $conn = connectMySQL();
    $service_id = intval($serviceID);
    $ticket_n = intval($ticketN);
    $sql = "DELETE FROM Queue WHERE TicketNumber = $ticket_n AND ServiceID=$service_id";
    if ($result = $conn->query($sql)) {
        return ($conn->affected_rows === 1);
    } else {
        printf("Error message: %s\n", $conn->error);
        return false;
    }
}

/**
 * Aggiorna le statistiche in authentication e service
 * Si incrementano i clienti serviti
 * @param $serviceID
 * @return bool
 */
function update_stats($serviceID) {
    /*
     * update both Authentication and Service table
     * return true if affected rows are equal to 1
     * false instead
     */
    $conn = connectMySQL();
    $serviceID = intval($serviceID);
    $sql1 = "update Authentication set Counter=Counter+1 where ServiceID=$serviceID";
    $sql2 = "update Service set Counter=Counter+1 where ID=$serviceID";
    $result1 = $conn->query($sql1);
    $result2 = $conn->query($sql2);

    if ($result1 && $result2) {
        return ($conn->affected_rows === 1 && $conn->affected_rows === 1);
    } else {
        printf("Error message: %s\n", $conn->error);
        return false;
    }
}

/**
 * Get current queued ticket for a given service.
 * @param $service_id
 * @return -1 on failure
 */
function get_length_by_service_id($service_id) {
    //SELECT COUNT(ID) FROM Queue WHERE ServiceID=3
    $conn = connectMySQL();
    $serviceID = intval($service_id);
    $sql = "SELECT COUNT(ID) as counter FROM Queue WHERE ServiceID=" . $service_id;
    $queue = -1;
    if ($result = $conn->query($sql)) {
        if ($result->num_rows === 1) {
            $queue = $result->fetch_assoc();
        }
    } else {
        printf("Error message: %s\n", $conn->error);
        print("\n" . $serviceID);
    }
    return $queue["counter"];
}

/**
 * @return numero di servizi esistenti
 */
function get_total_service_num() {
    // Create connection
    $conn = connectMySQL();
    $side_content = '<p class="tally"></p>';
    $sql = "SELECT COUNT(*) as n FROM Service";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $side_content = '<p class="tally">' . $row["n"] . '</p>';
        $conn->close();
    }
    return $side_content;
}

/**
 * @return numero di persone in attesa su tutti i servizi
 */
function get_total_lenght() {
    $conn = connectMySQL();
    $paragraph_content = '<p class="tally"></p>';
    $sql = "SELECT COUNT(*) as n FROM Queue";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $paragraph_content = '<p class="tally">' . $row["n"] . '</p>';
        $conn->close();
    }
    return $paragraph_content;
}

function get_currently_served_ticket_by($service_name) {
    return get_bottom($service_name);
}

?>