html
  body
    div. 
      Okays Multiline
      some text

    <?php if(count($_SERVER) and Ant::iterable($_SERVER)): foreach($_SERVER as $k=>$item): ?>
    <div><?php echo $k;?> -> <?php echo $item;?></div>
    <?php endforeach; else: ?>
      :(
    <?php endif; ?>

    <?php class X {public function __construct(){}};?>

    style.
      body {
        color: red;
      }

    script.
    	if(true){
    		console.log('Jade!')
    	}