//ナビゲーションメニュー
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
        clearTimeout(timeoutId);
      }
    }
    countUp();
  }
  