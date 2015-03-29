<?php
class Microjade{
  protected $patterns = array(
    'block' => '~^(if|else|elseif|for|foreach|while|block)\b\s*(.*)$~',
    'php' => '~^(\-|=|\$|\!=?)\s*(.*)$~',
    'html' => '~^([\w\d_\.\#\-]+)(.*)$~',
    'comment' => '~^(//\-?)\s*(.*)$~',
    'text' => '~^(\|)?(.*)$~',
  );
  public function compile($input, $showIndent = false){
    $lines = explode("\n", str_replace("\r", '', rtrim($input, " \t\n") . "\n"));
    $output = $textBlock = $phpCode = null;
    $closing = array();
    foreach ($lines as $n => $line){
      $token = $this->createToken();
      $nextLine = isset($lines[$n + 1]) ? $lines[$n + 1] : '';
      $indent = mb_strlen($line) - mb_strlen(ltrim($line));
      $nextIndent = mb_strlen($nextLine) - mb_strlen(ltrim($nextLine));
      $token->isBlock = ($nextIndent > $indent);
      $token->line = trim($line, "\t\n ");
      $indentStr = ($showIndent && !$textBlock) ? str_repeat(' ', $indent) : '';
      if (trim($line) == '' && !($n === count($lines) - 1 || mb_strpos($nextLine, '<?php') === 0))
        $indentStr = !$indent = PHP_INT_MAX;
      elseif ($textBlock !== null && $textBlock < $indent)
        $token->open = htmlspecialchars(ltrim($line));
      else{
        $token = $this->parseLine($token);
        $textBlock = null;
      }
      foreach (array_reverse($closing, true) as $i => $code){
        if ($i >= $indent){
          if (!$token->else || $i != $indent)
            $output .= $code;
          unset($closing[$i]);
        }
      }
      if ($n !== 0) $output .= "\n";
      if (mb_strpos($line, '<?php') === 0) $phpCode = true;
      if ($phpCode){
        $output .= "$line";
        if (mb_strpos($line, '?>') === 0) $phpCode = false;
        continue;
      }
      $output .= $indentStr . $token->open;
      $closing[$indent] = $token->close;
      if ($token->textBlock) $textBlock = $indent;
    }
    return rtrim($output, " \t\n") . "\n";
  }
  protected function createToken($line = null){
    return (object) array('open' => null, 'close' => null, 'line' => $line,
      'else' => false, 'textBlock' => false, 'isBlock' => false);
  }
  protected function parseLine($token){
    if (is_string($token))
      $token = $this->createToken($token);
    foreach ($this->patterns as $name => $pattern){
      if (preg_match($pattern, $token->line, $match)){
        $token->match = $match;
        if ($name == 'text')
          $token->open = $this->parseInline($match[2]);
        elseif ($name == 'comment'){
          $token->open = '<?php /* ' . $match[2];
          $token->close = ' */ ?>';
          $token->textBlock = true;
        }
        else
          $token = call_user_func(array($this, "parse" . ucfirst($name)), $token);
        break;
      }
    }
    return $token;
  }
  protected function parseInline($input){
    return preg_replace_callback('~{ (/?) ([^\}\n]*) }~x', function($m){
      if (preg_match($this->patterns['block'], $m[2])
         || preg_match($this->patterns['php'], $m[2])){
        $token = $this->parseLine($m[2]);
        return empty($m[1]) ? $token->open : $token->close;
      }
      return $m[0];
    }, $input);
  }
  protected function parseBlock($token){
    list($type, $code) = array_slice($token->match, 1, 2);
    if ($type == 'block'){
      $token->open = "<?php if(isset(\$$code)) echo \$$code; else{ ob_start();\$_blocks[]=\"$code\" ?>";
      $token->close = '<?php $_block=array_pop($_blocks);echo $$_block=ob_get_clean();}?>';
    }
    else{
      $code = preg_replace('~^\s*\( (.*) \)\s*$~x', '\\1', $code);
      $token->open = "<?php $type ($code){ ?>";
      if ($type == 'else') $token->open = "<?php } $type{ ?>";
      $token->close = "<?php } ?>";
      $token->else = in_array($type, array('else', 'elseif'));
    }
    return $token;
  }
  protected function parsePhp($token){
    list($type, $code) = array_slice($token->match, 1, 2);
    if ($type == '-'){
      $token->open = "<?php $code ?>";
      if (preg_match($this->patterns['block'], $code))
        $token = $this->parseLine($code);
    }
    elseif ($type == '!=' || $type == '!')
      $token->open = "<?php echo $code ?>";
    elseif ($type == '=')
      $token->open = "<?php echo htmlspecialchars($code, ENT_QUOTES) ?>";
    elseif ($type == '$')
      $token->open = "<?php echo htmlspecialchars(\$$code, ENT_QUOTES) ?>";
    return $token;
  }
  protected function parseHtml($token){
    $m = array_fill(0, 5, null);
    preg_match('~^([\w\d\-_]*[\w\d])? ([\.\#][\w\d\-_\.\#]*[\w\d])?
      (\( (?:(?>[^()]+) | (?3))* \))? (/)? (\.)? ((\-|=|\!=?)|:)? \s* (.*) ~x', $token->line, $m);
    $token->open = empty($m[1]) ? '<div' : "<$m[1]";
    $token->close = empty($m[1]) ? '</div>' : "</$m[1]>";
    if (!empty($m[2])){
      $id = preg_filter('~.*(\#([^\.]*)).*~', '\2', $m[2]);
      $token->open .= $id ? " id=\"$id\"" : '';
      $classes = preg_replace('~\#[^\.]*~', '', $m[2]);
      $classes = str_replace('.', ' ', $classes);
      $token->open .= $classes ? ' class="' . trim($classes) . '"' : '';
    }
    if (!empty($m[3]))
      $token->open .= ' ' . $this->parseInline(trim($m[3], '() '));
    $token->close = empty($m[4]) ? $token->close : '';
    $token->open .= empty($m[4]) ? '>' : " />";
    $token->textBlock = !empty($m[5]);
    if (!empty($m[6])){
      $nextToken = $this->createToken($m[7] . $m[8]);
      $nextToken->isBlock = $token->isBlock;
      $nextToken = $this->parseLine($nextToken);
      $token->open .= $nextToken->open;
      $token->close = $nextToken->close . $token->close;
    }
    else
      $token->open .= $this->parseInline($m[8]);
    return $token;
  }
}