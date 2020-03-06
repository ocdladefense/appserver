<?php
class CarDB{

    private $data;
    private $connection;

    function __construct($data){
        $this->data = $data;
    }

    function prepareData(){
        $this->subject_1 = addslashes($this->data->subject_1);
        $this->subject_2 = addslashes($this->data->subject_2);
        $this->summary = addslashes($this->data->summary);
        $this->result = addslashes($this->data->result);
        $this->title = addslashes($this->data->title);
        $this->plaintiff = addslashes($this->data->plaintiff);
        $this->defendant = addslashes($this->data->defendant);
        $this->citation = addslashes($this->data->citation);
        $this->month = addslashes($this->data->month);
        $this->date = addslashes($this->data->date);
        $this->year = addslashes($this->data->year);
        $this->circut = addslashes($this->data->circut);
        $this->majority = addslashes($this->data->majority);
        $this->judges = addslashes($this->data->judges);
        $this->url = addslashes($this->data->url);
    }

    function connect(){
                // Create connection
        $this->connection = new mysqli(SERVER_NAME,USER_NAME,PASSWORD);

        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    function insert(){
        echo "Connected successfully";
        $this->connection = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DATABASE_NAME);
        $query = "INSERT INTO cars (subject_1, subject_2, summary, result, title, plaintiff, defendant, citation, month, date, year, circut, majority, judges,url)
        VALUES ('$this->subject_1','$this->subject_2','$this->summary','$this->result','$this->title','$this->plaintiff','$this->defendant','$this->citation','$this->month', $this->date, $this->year,'$this->circut','$this->majority','$this->judges','$this->url')";

        if ($this->connection->query($query) === TRUE) {
            echo "<br><strong>New record created successfully<br></strong>";
        } else {
            echo "<br><strong>ERROR CREATING RECORD: <br>" . $query . "<br>" . $this->connection->error . "<br></strong>";
        }
    }
    
    function close(){
        $this->connection->close();
    }
}