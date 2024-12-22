import mysql.connector
from mysql.connector import Error

def stop_slave(connection):
    cursor = connection.cursor()
    cursor.execute("STOP SLAVE;")
    connection.commit()

def get_master_status(connection):
    cursor = connection.cursor()
    cursor.execute("SHOW MASTER STATUS;")
    result = cursor.fetchone()
    return result  # Returns (File, Position, Binlog_Do_DB, Binlog_Ignore_DB, Executed_Gtid_Set)

def configure_replication(connection, master_host, master_user, master_password, master_log_file, master_log_pos):
    cursor = connection.cursor()
    change_master_query = f"""
        CHANGE MASTER TO 
            MASTER_HOST='{master_host}', 
            MASTER_USER='{master_user}', 
            MASTER_PASSWORD='{master_password}', 
            MASTER_LOG_FILE='{master_log_file}', 
            MASTER_LOG_POS={master_log_pos};
    """
    cursor.execute(change_master_query)
    connection.commit()

def start_slave(connection):
    cursor = connection.cursor()
    cursor.execute("START SLAVE;")
    connection.commit()

def sync_databases():
    try:
        # Connect to both MySQL servers (adjust hostnames/IPs as needed)
        db1_connection = mysql.connector.connect(host="44.209.52.21", user="repl", password="replpassword", database="registration_site")
        db2_connection = mysql.connector.connect(host="52.202.33.252", user="repl", password="replpassword", database="registration_site")

        # Stop slave replication on both servers before fetching the master status
        stop_slave(db1_connection)
        stop_slave(db2_connection)

        # Get master status from both servers
        db1_master_status = get_master_status(db1_connection)
        db2_master_status = get_master_status(db2_connection)

        # Ensure the databases are selected correctly before configuring replication
        db1_connection.database = 'registration_site'
        db2_connection.database = 'registration_site'

        # Exchange master status info and configure each server to replicate from the other
        configure_replication(db1_connection, "52.202.33.252", "repl", "replpassword", db2_master_status[0], db2_master_status[1])
        configure_replication(db2_connection, "44.209.52.21", "repl", "replpassword", db1_master_status[0], db1_master_status[1])

        # Start replication on both servers
        start_slave(db1_connection)
        start_slave(db2_connection)

        print("Replication setup complete on both servers.")
    except Error as e:
        print(f"Error: {e}")
    finally:
        # Close the connections
        if db1_connection.is_connected():
            db1_connection.close()
        if db2_connection.is_connected():
            db2_connection.close()

if __name__ == "__main__":
    sync_databases()
