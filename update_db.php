<?php
include 'db_connect.php';
$conn->query("UPDATE playground_games SET title='Universal Logic Gate Simulator', game_file_path='assest/games/logic_gate_simulator.html', description='Interactive simulator for AND, OR, NOT, NAND, NOR, XOR, XNOR gates.' WHERE title='AND Gate Logic Simulator'");
echo "Database updated!";
?>
