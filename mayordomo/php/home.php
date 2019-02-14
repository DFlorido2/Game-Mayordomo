<?php 

/*El código está adaptado para que independientemente de la dimensión de la tabla cada jugador tenga las mismas 
"fichas" al inicio y que esté lo más cerca posible del 40%, ya que aveces no es posible hacerlo exacto, porque algunos valores tienen decimales. 
Por eso redondeo al número entero más próximo en dirección abajo. Lo ideal es jugar con una tabla de dimensiones 10/20/40/50, es decir que al hacer
el 40% de la dimensión de la tabla dé un número entero.*/

$GLOBALS['tableDimension'] = 20; 

function startGame(){
 
  $tableDimension = $GLOBALS['tableDimension'];
  $turn = rand(1,2);
  $table = array();
  $percentValue = floor(($tableDimension * 0.4) / 2);
  $totalPlayers = 2;  

  for($i=0; $i<$tableDimension; $i++){

	$table[$i] = 0;
  }

 /***** RELLENAR PLAYERS *****/
 
  for($p=0; $p<$totalPlayers; $p++){
		
		$countValuesPlayer = 0;
		
		do{
	  
		  $randomPosition = rand(0,$tableDimension-1);
		  
		  if($table[$randomPosition] == 0){
			  
			  $table[$randomPosition] = $p+1;
			  $countValuesPlayer++;
		  }
	  
		}while ($countValuesPlayer != $percentValue);
   }
 
 /***** FIN RELLENAR PLAYERS *****/
 
  $error = "false"; 
  $winner = 0;	
  
  $gameStatus  =  array (
    'turn'  =>  $turn,
    'table'  =>  $table,
	'error' => $error,
	'winner' => $winner,
  );

  echo json_encode($gameStatus);
}

function userMove(){
	
	$currentMove = $_POST['currentMove'];
	$currentTable = json_decode($_POST['currentTable']);
	$turn = $_POST['turn'];

	if($currentTable[$currentMove] != $turn){ /*Es decir, que el jugador que juega no ha hecho click en una casilla de su color*/
		
		$error = "false";
		$currentTable[$currentMove] = $turn;
		
		/***** RECORRER LA TABLA A LA IZQUIERDA BUSCANDO QUE FICHAS COMER *****/
		
		$firstLeft = -1; /*El primero por la izquierda del color del jugador que juega, la declaro como -1 por si no encuentra ninguna a la izquierda*/
		$i = $currentMove;
		$printLeft = array();
		
		if($currentMove != 0){ /*Si no ha hecho click en la primera casilla del tablero*/
			
			do{
				
				$i--;
				
				if($currentTable[$i] == $turn){
					$firstLeft = $i;
				}
					
			}while($firstLeft == -1 && $i > 0);
		}

		if($firstLeft != -1 && ($firstLeft != $currentMove-1)){ /*Esto quiere decir que ha encontrado un cuadrado del mismo color del jugador a la izquierda y que no es el que está pegado, es decir que hay cuadrados en medio*/
			
			$objective = $currentTable[$currentMove-1]; /*El objetivo es que los cuadrados de en medio de los dos sean iguales para comerlos (o todos blancos o todos del color rival, para ello cojo como referencia el primer cuadrado del movimiento a la izquierda)*/
			$eat = true;
			
			for($j=$firstLeft+1; $j<$currentMove; $j++){

				if(($currentTable[$j] == $objective) && ($eat)){
					$eat = true;
				}
				else{
					$eat = false;
				}	
			}
			
			if($eat){
				
				$l=0;
				
				for($j=$firstLeft+1; $j<$currentMove; $j++){
					
					$printLeft[$l] = $j; /*Me guardo las posiciones que quiero pintar a la izquierda en este array para no modificar la tabla, ya que aún tengo que revisar las posiciones de la derecha*/
					$l++;
				}			
			}
		}
		
		/***** FIN RECORRER LA TABLA A LA IZQUIERDA BUSCANDO QUE FICHAS COMER *****/
		
		/***** RECORRER LA TABLA A LA DERECHA BUSCANDO QUE FICHAS COMER *****/
		
		$firstRight = -1; /*El primero por la izquierda del color del jugador que juega, la declaro como -1 por si no encuentra ninguna a la derecha*/
		$i = $currentMove;
		$tableDimension = $GLOBALS['tableDimension'];
		$printRight = array();
		
		if($currentMove != $tableDimension-1){ /*Si no ha hecho click en la última casilla del tablero*/
			
			do{
				
				$i++;
				
				if($currentTable[$i] == $turn){
					$firstRight = $i;
				}

			}while($firstRight == -1 && $i < $tableDimension-1);
		}

		if($firstRight != -1 && ($firstRight != $currentMove+1)){ /*Esto quiere decir que ha encontrado un cuadrado del mismo color del jugador a la derecha y que no es el que está pegado, es decir que hay cuadrados en medio*/
			
			$objective = $currentTable[$currentMove+1]; /*El objetivo es que los cuadrados de en medio de los dos sean iguales para comerlos (o todos blancos o todos del color rival, para ello cojo como referencia el primer cuadrado del movimiento a la derecha)*/
			$eat = true;
			
			for($j=$firstRight-1; $j>$currentMove; $j--){

				if(($currentTable[$j] == $objective) && ($eat)){
					$eat = true;
				}
				else{
					$eat = false;
				}	
			}
			
			if($eat){
				
				$l=0;
				
				for($j=$firstRight-1; $j>$currentMove; $j--){

					$printRight[$l] = $j; /*Me guardo las posiciones que quiero pintar a la derecha en este array*/
					$l++;
				}			
			}
		}

		/***** FIN RECORRER LA TABLA A LA DERECHA BUSCANDO QUE FICHAS COMER *****/
		
		/***** MODICAR EL TABLERO *****/
		
		if(count($printLeft) !=0) { /*Si hay posiciones que pintar a la izquierda*/
		
			for($m=0; $m < count($printLeft); $m++){
			
				$currentTable[$printLeft[$m]] = $turn;
			}
		}
		
		if(count($printRight) !=0) { /*Si hay posiciones que pintar a la derecha*/
		
			for($m=0; $m < count($printRight); $m++){
			
				$currentTable[$printRight[$m]] = $turn;
			}
		}
		
		/***** FIN MODICAR EL TABLERO *****/
	}
	
	else{
		   
		$error = "true";
	}
	
	if($turn == 1){
		$turn = 2;
	}
	
	else{
		$turn = 1;
	}
	
	/***** COMPROBAR SI HAY UN GANADOR *****/
		
	$winner = 0;	
	$currentTableCount = array_count_values($currentTable);
	
	if(isset($currentTableCount[1]) && !isset($currentTableCount[2])){ /*Si no ha encontrado ningún valor del jugador número 2, gana el 1*/
		$winner = 1;
	}
	
	else if(!isset($currentTableCount[1]) && isset($currentTableCount[2])){ /*Si no ha encontrado ningún valor del jugador número 1, gana el 2*/
		$winner = 2;
	}
		
	/***** FIN COMPROBAR SI HAY UN GANADOR *****/
	
	 $gameStatus  =  array (
		'turn'  =>  $turn,
		'table'  =>  $currentTable,
		'error' => $error,
		'winner' => $winner,
	);
	
	echo json_encode($gameStatus);
}

$function = $_POST['function'];

if(isset($_POST['function']) && !empty($_POST['function'])) {

    switch($function) {
		
        case 'startGame': 
            startGame();
        break;
		
		case 'userMove': 
            userMove();
        break;
    }
}

?>