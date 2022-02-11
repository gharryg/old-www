$(document).ready(function(){
    if(window.addEventListener){
        var keys = [], konami = "38,38,40,40,37,39,37,39,66,65,13";
        window.addEventListener("keydown", function(e){
            keys.push(e.keyCode);
            if (keys.toString().indexOf(konami) >= 0) {
                changeBoard();
                keys = [];
            };
        }, true);
    };
});

function changeBoard(){
    var board = Math.floor(Math.random() * (0 - 3) +3);
    switch(board){
        case 0:
            $('#board').css("background-image", "url(penteAssets/images/secretBoards/boardDoge.png)");
            break;
        case 1:
            $('#board').css("background-image", "url(penteAssets/images/secretBoards/magicPonyBoard.png)");
            break;
        case 2:
            $('#board').css("background-image", "url(penteAssets/images/secretBoards/oprahBoard.png)");
            break;
        default:
            break;
    }
}