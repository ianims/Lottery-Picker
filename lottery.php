<html>
<body style="background-color:#8e9483;">
<form method="post" action="lottery.php">
<h3>LOTTERY NUMBER GENERATOR</h3>
<label>Select Game:</label>
<select id="game" name="game"><option value="0">Select game...</option><option value="1">Lottery</option><option value="2">Euro Millions</option><option value="3">Thunderball</option></select>
<br /><br />
<label>Output to...:</label>
<select id="out" name="out"><option value="0">Select Output...</option><option value="1">Screen</option><option value="2">Text File</option></select>
<br /><br />
<label>Number of Lines</label>
<input type="text"  id="n1" name="n1" />
<br /><br />
<button type="submit" id="submit" name="submit"  value="submit">GO</button>
<input type="hidden" id="submitted" value="subbmitted" />
</form>

</body>
</html>


<?php
//
// Written by		:	I.M.Sherman
// Company			:	ITECH
// Date				:	30.12.2014
// Version			:	1.0.0
//
if ( isset($_POST['submit'])) {
	if (  ( $_POST['game'] == '0' )  || ( $_POST['out'] == '0' ) || ( strlen($_POST['n1']==0 ) ) ) {
		echo '<b>Please select game, output and enter number of lines greater than 1 </b>';
	} else {
		$L = new Lotto();
	}
}


class Out_File
{
	function Out_File( $ar , $bonus , $num_game_numbers , $num_game_bonus )
	{
		$myfile = fopen("L.txt", "w") or die("Unable to open file!");
		$cl=0;
		echo 'Created File : L.txt';
		foreach ($ar as $L)
		{
				$L1="";
				for($x=0; $x <= $num_game_numbers-1;  $x++ ) {
					$L1 .= $L[$x] . ', ';
				}
				if ( $num_game_bonus != 0  ) { 
					$L1 .= 'Bonus: [';
					for($i=0; $i <= $num_game_bonus-1;  $i++ ) {
							$L1 .= $bonus[$cl][$i].',';
					}
					$L1 .= ']';
				}
				$cl++;
				fwrite($myfile, $L1  );
				fwrite($myfile , PHP_EOL   ); //write to txtfile
			}
			fclose($myfile);
			echo ' Finished creating file!!';
	}
}



class Out_Screen
{
	function Out_Screen( $ar , $bonus , $num_game_numbers , $num_game_bonus , $cost)
	{
		ob_start();
		//
		// loop round array.
		//
		$cl=0;
		
		echo 'COST:£'.$cost.'.00<br />';
		
		foreach ($ar as $L)
		{
				$L1="";
				for($x=0; $x <= $num_game_numbers-1;  $x++ ) {
					$L1 .= $L[$x] . ', ';
				}
				if ( $num_game_bonus != 0  ) { 
					$L1 .= 'Bonus: [';
					for($i=0; $i <= $num_game_bonus-1;  $i++ ) {
							$L1 .= $bonus[$cl][$i].',';
					}
					$L1 .= ']';
				}
				$cl++;
				echo $L1;
				echo '<br />';
		}
		ob_flush();
	}

}



class Lotto {
	public $all_nums;
	public $new1;
	public $game;
	public $out1;
	public $num_lines;
	public $num_game_numbers;
	public $num_game_bonus;
	public $num_bonus_range;
	public $range_of_numbers;
public $cost;
	
	
	function Lotto()
	{
			ini_set('max_execution_time', 50); 
			$all_nums=array();
			$different_numbers = 3;
			$this->new1 =array();
			// get input values.
			$this->game      = $_POST['game'];
			$this->out1      = $_POST['out'];
			$this->num_lines = $_POST['n1'];
			// set game parameters...
			switch ( $this->game) {
				case '1':  $this->num_game_numbers=6;   $this->range_of_numbers = 49; $this->num_game_bonus=0;  $this->num_bonus_range=0; $this->cost=2; break;	// lotto.
				case '2':  $this->num_game_numbers=5;   $this->range_of_numbers = 50; $this->num_game_bonus=2;  $this->num_bonus_range=11; $this->cost=2.5;  break;	// euro millions.
				case '3':  $this->num_game_numbers=5;   $this->range_of_numbers = 39; $this->num_game_bonus=1;  $this->num_bonus_range=14;  $this->cost=1; break;	// thunderball.
			}
			// execute function...
			$this->process();
	}



	function process() {
		 // no. of different no's per line -- e.g. lines will not have $different_numbers the same, so if the no's picked are 1,2,3 no other line will 1,2,3
		$different_numbers =3; 
		$bonus=array();

		$cost=$this->cost * $this->num_lines;
		
		for($x=0; $x<=$this->num_lines; $x++ ) {
			$numbers = range(  1, $this->range_of_numbers  );
			shuffle($numbers);
			$numbers = array_slice( $numbers, 0, $this->num_game_numbers  );
			asort(  $numbers  );
			$num_same = $this->check_nums(  $numbers  ,  $this->new1  );
			//
			// if there are too many similar numbers selected to previous lines decrease X so
			// the numbers are picked again..otherwise add them to the array new1.
			//
			if ( count($num_same) >= $different_numbers && $x > 0 ) { $x--; } else {
				array_push(  $this->new1, $numbers );
			}
			//
			// any bonus no's required?
			//
			if ( $this->num_game_bonus != 0) {
				$numbers = range(  1, $this->num_bonus_range  );
				shuffle($numbers);
				array_push(  $bonus ,  array_slice( $numbers, 0, $this->num_game_bonus  ));
			}
		}
		asort($this->new1);
		
		//
		// where is output going??
		//
		if ( $_POST['out'] == '1' ) 
		{
			$o = new Out_Screen( $this->new1, $bonus,  $this->num_game_numbers, $this->num_game_bonus , $cost  );
		}
		else
		{
			$o= new Out_File ( $this->new1, $bonus,  $this->num_game_numbers, $this->num_game_bonus);
		}

	}

	
	
	private function check_nums($chk,$new1)
	{
		if ( count($new1) != 0 ) {
			$result = array_intersect(  $chk  ,   $new1[0]  );
		} else { $result=array(); }
		return $result;
	}

	
	
}



?>
