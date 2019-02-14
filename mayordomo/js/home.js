function startGame(){

    $.ajax({
    	
        url: "php/home.php",
       	type: "post",
       	data: "function=startGame",
       
       success: function(data){

        game = JSON.parse(data);
        gameStatus(game);
       }
	  });
}

function userMove(e){

  currentMove = e.target.id;
  currentTable = [];
  i=0;

  $('.cell').each(function (index) {

      currentTable[i] = $(this).attr('player');
      i++;   
  });

  currentTable = JSON.stringify(currentTable);

    $.ajax({
      
        url: "php/home.php",
        type: "post",
        data: {'function':"userMove",'currentMove': currentMove, 'currentTable': currentTable, 'turn': turn},
       
       success: function(data){

        game = JSON.parse(data);
        gameStatus(game);
       }
  });

}

function gameStatus(game){

  if(game.error == "false"){

    $('#error').addClass('hidden');
    $('#table').empty();

    for(i=0; i< game.table.length; i++){

      $("#table").append('<div id="'+i+'" class="cell player'+game.table[i]+'" player="'+game.table[i]+'"></div>');
    }

     if(game.winner == 0){

        $('#turn').text("Turno del jugador "+game.turn);
        $('#turn').removeClass();

        turn = game.turn;

        if(turn == 1){

          $('#turn').addClass('blue');
        }

        else{

          $('#turn').addClass('red');
        }

        inputs = document.getElementsByClassName('cell');

        for(j=0; j<inputs.length; j++){

            inputs[j].addEventListener("click", userMove);
        }

      }

      else{

        if(game.turn == 1){

          winner = 2;
        }

        else{
          winner = 1;
        }

        $('#turn').text("ENHORABUENA JUGADOR " +winner+ " HAS GANADO!")
      }  
     
  }

  else{

    $('#error').removeClass('hidden');
  }

}

$(document).ready(function() {

  var turn = 0;
  startGame();
  
	$('#startGame').on('click', function(e) {

		startGame();
	})

});