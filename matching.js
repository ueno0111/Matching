function sample(){

  //ここに演算子、IF文入れてゆく
  function hikaku(a, b) {
  var str1 = a.toUpperCase();
  var str2 = b.toUpperCase();
  if (str1 == str2) { return 0; }
  if (str1 > str2) { return -1; }
  if (str1 < str2) { return 1; }
}

var arr = ["9", "7", "3", "1", "6", "5"];
arr.sort(hikaku);
console.log(arr);           // => ["a", "B", "c", "x", "y", "Z"]

//ここに演算子、IF文入れてゆく

}




document.querySelectorAll('.scroll').forEach(elm => {
	elm.onscroll = function () {
		if (this.scrollTop + this.clientHeight >= this.scrollHeight) {
			//スクロールが末尾に達した
			if (parseInt(this.dataset.lastnum) < parseInt(this.dataset.max)) {
				//未ロードの画像がある場合
				this.dataset.lastnum = parseInt(this.dataset.lastnum) + 1;
				let img = document.createElement('img');
				img.src =  this.dataset.lastnum +'.jpg';
				this.appendChild(img);
			}
		}
	};
});


$(function() {
    $('.hamburger').click(function() {
        $(this).toggleClass('active');
 
        if ($(this).hasClass('active')) {
            $('.globalMenuSp').addClass('active');
        } else {
            $('.globalMenuSp').removeClass('active');
        }
    });

//今すぐ登録するのモーダルウインドウ//
$('#openModal').click(function(){
//   alert ('aaaaaa');
  $('#modalArea').fadeIn();
    });
    $('#closeModal , #modalBg').click(function(){
      $('#modalArea').fadeOut();
    });

//簡単無料登録のモーダルウインドウ//
$('#openModal2').click(function(){
  $('#modalArea2').fadeIn();
  });
    $('#closeModal2 , #modalBg2').click(function(){
      $('#modalArea2').fadeOut();
      });

//ログインのモーダルウインドウ//
$('#openModal3').click(function(){
  $('#modalArea3').fadeIn();
  });
    $('#closeModal3 , #modalBg3').click(function(){
      $('#modalArea3').fadeOut();
      });
        });


  //partner.php　いいね！アラート
  //function basicSample(){
    //swal("いいね！を送りました");
 // }


  function oopsSwalImageSample() {
    Swal.fire({
      title: 'いいね！を送りました',
      text: 'カスタムイメージ付モーダル.',
      imageUrl: 'https://unsplash.it/400/200',
      imageWidth: 400,
      imageHeight: 200,
      imageAlt: 'Custom image',
    })

    let count = 0;
    const countUp = () => {
      console.log(count++);
      var id = setTimeout(countUp, 1000);
      if(count > 5){　
        clearTimeout(timeoutId);　//idをclearTimeoutで指定している
      }
    }
    countUp();
  }

  
  
  