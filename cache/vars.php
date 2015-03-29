<!DOCTYPE html>
<html>
  <head>
    <title name="oj"><?php echo date('Y-m-d');?></title>
  </head>
  <body>
    Hello Multiline text
    <?php class X { public function __construct(){ echo __DIR__; } } new X(); ;?>
    <div>divline text</div>
    <style>
      body {
        color: red;
      }</style>
  </body>
</html>