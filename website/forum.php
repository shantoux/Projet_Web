<!-- Web page for the annotation forum -->

<?php session_start();?>

<!DOCTYPE html>
<html>
  <!-- Page header -->
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forum</title>
    <link rel="stylesheet" type="text/css" href="./style.css" /s>
  </head>

  <body>
    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a href="./search.php">New search</a>
        <?php
          if ($_SESSION['role'] == 'Annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a class=\"active\" href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a class=\"active\" href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a class=\"active\" href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users' List</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="login.php">Disconnect</a>
        <a class="disc"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>
    </div>

    <!-- Display page title -->
    <div id="pagetitle">
      Annotators Forum
    </div>

    <div class="center">
      This is the annotation forum. Create a conversation with other annotators to help on any question or difficulty.
    </div>

    <?php
      // import db function
      include_once 'libphp/dbutils.php';
      connect_db();

      // add message in the database if one has just been written
      if (isset($_POST["send_message"])) {
        $new_message = array();
        $new_message['topic_name'] = urldecode($_GET['topic']);
        $new_message['user_email'] = $_SESSION['user'];
        $new_message['message'] = $_POST['message'];
        $result_insert = pg_insert($db_conn, 'database_projet.messages', $new_message);
      }

      echo '<div class="center">';
      // add button to instanciate new conversation
      if (!isset($_POST["creating"])) {
        echo '<form action="forum.php" method = "post">';
        echo '<input type="submit" value="Create new topic" name="creating">';
        echo '</form>';
      }
      // chose conversation participants and topic name if the topic instanciation button has been clicked
      else {
        echo 'Chose who will be part of the conversation:<br>';
        echo '<span class="small_text">Hold \'ctrl\' to select multiple users</span><br>';
        echo '<form action="forum.php" method = "post">';
        echo '<select name="selected_users[]" ';
        // retrieve all validated users and display multiple-selection menu
        $query_users = "SELECT email, last_name, first_name, role FROM database_projet.users WHERE status = 'validated';";
        $result_users = pg_query($db_conn, $query_users) or die('Query failed with exception: ' . pg_last_error());
        echo 'multiple size = ' . pg_num_rows($result_users) . '>';
        while ($user = pg_fetch_array($result_users)) {
          // check if user is different from current user (who has no choice but to take part in the discussion)
          if ($user["email"] != $_SESSION['user']) {
            echo '<option value=' . $user["email"] . '>' . $user["first_name"] . " " . $user["last_name"] . " (" . $user["role"] . ")" . '</option>';
          }
        }
        echo '</select><br><br>';
        echo '<br><input type="text" id="name" name="topic_name" required> <label for="name">Chose topic name</label><br><br>';
        echo '<br><br><input type="submit" value="Create" name="create">';
        echo '</form>';
      }
      // create the new topic if the Create button has been clicked
      if (isset($_POST["create"])) {
        // create topic in DB
        $new_topic = array();
        $new_topic['name'] = $_POST['topic_name'];
        $result_insert_1 = pg_insert($db_conn, 'database_projet.topics', $new_topic);

        // add all involved annotators...
        foreach ($_POST['selected_users'] as $user_email) {
          $new_conv_member = array();
          $new_conv_member['topic_name'] = $_POST['topic_name'];
          $new_conv_member['user_email'] = $user_email;
          $result_insert_2 = pg_insert($db_conn, 'database_projet.correspondents', $new_conv_member);
        }

        // ...including current user
        $new_conv_member = array();
        $new_conv_member['topic_name'] = $_POST['topic_name'];
        $new_conv_member['user_email'] = $_SESSION['user'];
        $result_insert_2 = pg_insert($db_conn, 'database_projet.correspondents', $new_conv_member);

        // check if all went well
        if ($result_insert_1 && $result_insert_2) {
          echo "<td> Successfully added</td>";
        } else {
          echo "<td> Something went wrong.</td>";
        }
      }
      echo '</div>';

      ### Display all conversations

      // retrieve conversations in which user is involved
      $query_topics = "SELECT T.name, T.creation_date FROM database_projet.topics T, database_projet.correspondents C
      WHERE T.name = C.topic_name AND C.user_email = '" . $_SESSION['user'] . "' ORDER BY T.creation_date DESC;";
      $result_topics = pg_query($db_conn, $query_topics) or die('Query failed with exception: ' . pg_last_error());

      // loop on all conversations retrieved from the DB
      while ($topic = pg_fetch_array($result_topics)) {

        // initiate conversation table div
        echo '<div class="center">';
        echo '<table class="table_type_gene_inf">';
        echo '<colgroup>';
        echo '<col span="1" style="width: 80%;">';
        echo '<col span="1" style="width: 20%;">';
        echo '</colgroup>';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="type2"  align="left">';

        // display topic name
        echo $topic["name"];

        // fetch and display conversation participants when mouse-overing "Who can see this topic?"
        $query_participants = "SELECT U.last_name, U.first_name
        FROM database_projet.topics T, database_projet.correspondents C, database_projet.users U
        WHERE U.email = C.user_email AND C.topic_name = T.name AND T.name = '" . $topic["name"] . "';";
        $result_participants = pg_query($db_conn, $query_participants) or die('Query failed with exception: ' . pg_last_error());
        $title = "";
        while ($participant = pg_fetch_array($result_participants)) {
          $title .= $participant["first_name"] . " " . $participant["last_name"] . "\n";
        }
        echo '<span style="float:right;color:grey;" title="' . $title . '">';
        echo 'Who can see this topic?';
        echo '</span>';
        echo '</th>';

        // display topic creation date
        echo '<td class="dark_cell" align="center" horizontal-align="middle">';
        echo 'Topic created on ' . $topic["creation_date"];
        echo '</td>';
        echo '</tr>';
        echo '</thead>';

        //display topic's messages
        echo '<tbody>';
        echo '<tr>';
        echo '<td colspan="2">';

        // retrieve all messages for this conversation
        $query_messages = "SELECT M.message, M.emission_date, M.user_email, U.last_name, U.first_name
        FROM database_projet.topics T, database_projet.messages M, database_projet.users U
        WHERE T.name = M.topic_name AND M.user_email = U.email AND T.name = '" . $topic["name"] . "' ORDER BY M.emission_date ASC;";
        $result_messages = pg_query($db_conn, $query_messages) or die('Query failed with exception: ' . pg_last_error());

        // display all messages with informations on writer and emission date
        while ($message = pg_fetch_array($result_messages)) {
          echo '<span class="small_text">';
          echo 'On ' . $message["emission_date"] . ', ' . $message["first_name"] . ' ' . $message["last_name"] . ' (' . $message["user_email"] . ') wrote:<br>';
          echo '</span>';
          echo $message["message"] . '<br>';
        }
        echo '</td>';
        echo '</tr>';
        
        // add reply text input and button
        echo '<tr>';
        echo '<td colspan="2" class="dark_cell">';
        echo '<form action="./forum.php?topic=' . urlencode($topic["name"]) . '" method = "post">';
        echo '<input type="text" name="message" size="190%">';
        echo '<input type ="submit" value="Reply" name = "send_message">';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
      }
    ?>
  </body>
</html>
