<?php

$puzzle = array (); 
$puzzle['easy'] = /*{{{*/ <<<SUDOKU
xxx5x37xx
x6x9x21x8
54x8xxxx9
x73x564xx
x1xxxxx7x
xx573x29x
8xxxx9x63
6x13x4x2x
xx96x5xxx
SUDOKU;
/*}}}*/ 
$puzzle['medium'] =/*{{{*/ <<<SUDOKU
2xx3675xx
5xx8xxx6x
3xx45x7xx
x9x53x4xx
x8xxxxx7x
xx3x74x5x
xx1x26xx5
x3xxx5xx7
xx2783xx1
SUDOKU;
/*}}}*/
$puzzle['hard'] =/*{{{*/ <<<SUDOKU
xx716249x
xxxx8xx75
xxxx4x2xx
26xx53xx9
x9xxxxx2x
7xx29xx16
xx8x3xxxx
17xx2xxxx
x495178xx
SUDOKU;
/*}}}*/
$puzzle['vhard'] =/*{{{*/ <<<SUDOKU
2xx3x1x45
xxxxx6xx2
x53x9xxxx
98xxxx7x3
1x7xxx4x9
3x5xxxx86
xxxx2x96x
4xx1xxxxx
79x6x5xx4
SUDOKU;
/*}}}*/
$puzzle['vhard'] =/*{{{*/ <<<SUDOKU
xxxxxxxxx
x5xx4236x
3x46x817x
8xxx2xxx9
xxx8x1xxx
4xxx6xxx1
x162x49x7
x4957xx1x
xxxxxxxxx
SUDOKU;
/*}}}*/
$puzzle['hardest'] =/*{{{*/ <<<SUDOKU
85xxx24xx
72xxxxxx9
xx4xxxxxx
xxx1x7xx2
3x5xxx9xx
x4xxxxxxx
xxxx8xx7x
x17xxxxxx
xxxx36x4x
SUDOKU;
/*}}}*/
$puzzle['hardest'] =/*{{{*/ <<<SUDOKU
xx84xxxx1
x3xx2xx9x
2xxxx97xx
5xxxx32xx
x8xx4xx6x
xx78xxxx9
xx41xxxx3
x9xx5xx7x
7xxxx46xx
SUDOKU;
/*}}}*/
$puzzle['hardest'] =/*{{{*/ <<<SUDOKU
xx9xxx731
x38xx7xxx
xxx34x8xx
7xxxxxx53
89x5x6x47
x5xxxxxx6
xx6x59xxx
xxx2xxx1x
582xxx3xx
SUDOKU;
/*}}}*/

$puzzle['hardest'] =/*{{{*/ <<<SUDOKU
xx9xxx731
x38xx7xxx
xxx34x8xx
7xxxxxx53
89x5x6x47
x5xxxxx86
xx6x594xx
xxx2xxx1x
582xxx3xx
SUDOKU;
/*}}}*/


// @todo : Only possibility in square
// @todo : Only possibility in row eliminates in other square

Class Sudoku 
{
     private $raw = array();
     private $data = array(); 
     private $possible = array( );
     private $quiet = false ;

     private $log = array (); 
     public function __construct($string, $quiet = false  )/*{{{*/
     {
          $this->quiet = $quiet ;
          $x = 0 ;
          $y = 0 ; 
          foreach (explode("\n", $string) as $data )
          {
               for($x=0; $x<strlen($data); $x++)
               {
                    $a = substr($data,$x ,1) ;
                    if ( $a === 'x' )
                         $temp = null;
                    else
                         $temp = $a ;
                    $this->raw[$x][$y] = $temp ; 
                    $this->data[$x][$y] = $temp ; 
               }
               $y++ ;
          }
     }/*}}}*/
     public function display($ox= null, $oy = null )/*{{{*/
     {
          echo '<table border="1" cellpadding="10">';

          for ($y=0; $y < 9 ; $y++  )
          {
               echo '<tr>';
               for ( $x=0; $x< 9 ; $x++ )
               {
                    $val = $this->data[$x][$y] ;
                    if ( $ox !== null && $x == $ox && $oy !== null && $y == $oy ) 
                         echo '<td style="width:50px;background-color:#ff4444;" >'.$val.'</td>';
                    elseif ( $val === null)
                         echo '<td style="width:50px;font-size:75%;font-style:italic;background-color:#dddd00;">'.@implode(' ', $this->possible[$x][$y]).'</td>';
                    elseif ( $this->raw[$x][$y] == $this->data[$x][$y] )
                         echo '<td style="width:50px;background-color:#ddddff;"><b>'.$val.'</b></td>';
                    else
                         echo '<td style="width:50px;background-color:#eeffee;" >'.$val.'</td>';
                    if ( ( 1 + $x ) % 3 === 0) 
                         echo '<td></td>';
               }
               echo '</tr>';
               if ( ( 1 + $y ) % 3 === 0) 
                    echo '<tr><td colspan="12"></td></tr>';
          }
          echo '</table>';
     }/*}}}*/
     public function solve()/*{{{*/
     {
          $loop = 3 ;
          $hit = true; 
          while ( $hit && !$this->isSolved() )
          {
               while ( $hit && !$this->isSolved()  )
               {
                    $hit = false ;
                    while ( $this->fill() ) ; 

                    if ( $this->deduct() ) 
                         $hit = true ;
               }

               // $hit = $this->guessOneBox(); 
          }
          if ( !$this->quiet )
               echo "Gave up!"; 
          return $this->isSolved() ;
     }/*}}}*/
     private function setPoint($x, $y, $p )/*{{{*/
     {
          $this->data[$x][$y] = $p; 
     }/*}}}*/
     private function guessOneBox()/*{{{*/
     {
          for ( $c = 2; $c < 9; $c++ )
          {
               for ($y = 0; $y < 9 ; $y++ )
               {
                    for ($x = 0; $x < 9 ; $x++ )
                    {
                         if ( $this->data[$x][$y] === null && count( $this->possible[$x][$y] ) == $c )
                         {
                              foreach ( $this->possible[$x][$y] as $i => $p )
                              {
                                   $guess = new self( $this->export() ) ;
                                   echo "Guessing $x $y => $p<br />";
                                   $this->display();
                                   $guess->setPoint($x, $y, $p );
                                   try
                                   {
                                        $guess->solve() ; 
                                        // $this->data[$x][$y] = $p; 
                                        $this->data = $guess->data ;
                                        $this->log($x,$y,$p,"Good guess");
                                        return true ;
                                   }
                                   catch(exception $e )
                                   {
                                        $this->log($x,$y,$p,"Bad guess");
                                   }
                              }
                              if ( $x == 8 && $y == 1 && $p == 4 )
                              {
                                   die ("Back here !");
                              }
                         }
                    }
               }
          }
     }/*}}}*/
     private function export() /*{{{*/
     {
          $str = '';
          for ($y = 0; $y < 9 ; $y++ )
          {
               for ($x = 0; $x < 9 ; $x++ )
               {
                    $i = $this->data[$x][$y] ;
                    if ( $i === null )
                         $str .= 'x';
                    else
                         $str .= $i ;
               }
               $str .= "\n"; 
          }

          return trim($str );
     }/*}}}*/
     private function isSolved() /*{{{*/
     {
          for ($x = 0; $x < 9 ; $x++ )
               for ($y = 0; $y < 9 ; $y++ )
                    if ( $this->data[$x][$y] === null ) 
                         return false;
          return true ;
     }/*}}}*/

     private function fill()/*{{{*/
     {
          $x = 0;
          $y = 0;
          $hit = 0;

          $this->log(-1,-1,-1, 'Updating notes') ;
          for ( $y = 0; $y < 9 ; $y++ )
          {
               for ($x = 0; $x < 9; $x++ ) 
               {
                    if ( $this->data[$x][$y] != null ) continue ;

                    if ( !array_key_exists($x, $this->possible ) )
                         $this->possible[$x] = array();
                    if ( !array_key_exists($y, $this->possible[$x] ) )
                    {
                         $this->possible[$x][$y] = array(); 
                         for ($i = 1; $i<= 9; $i++ )
                              if ( $this->isValid($x,$y,$i ))
                                   $this->possible[$x][$y][] = $i ;
                    }
                    else
                    {
                         $temp = $this->possible[$x][$y] ;
                         $this->possible[$x][$y] = array(); 
                         foreach ($temp as $i )
                              if ( $this->isValid($x,$y,$i ))
                                   $this->possible[$x][$y][] = $i ;
                    }

                    if ( count($this->possible[$x][$y] ) == 1 )
                    {
                         $this->data[$x][$y] = $this->possible[$x][$y][0] ;
                         $this->log($x, $y, $this->data[$x][$y], "Fill"); 
                         $hit++; 
                    }
                    elseif ( count($this->possible[$x][$y]) == 0 )
                    {
                         if ( !$this->quiet ) 
                              $this->display($x, $y ); 
                         throw new exception("Nothing is possible in $x x $y"); 
                    }
               }
          }
          if ( $hit )
               return true ;
          return false; 
     }/*}}}*/
     private function deduct()/*{{{*/
     {
          $x = 0;
          $y = 0;
          $hit = 0;

          $this->log(-1,-1,-1, 'Updating notes') ;
          for ( $y = 0; $y < 9 ; $y++ )
          {
               for ($x = 0; $x < 9; $x++ ) 
               {
                    if ( $this->data[$x][$y] !== null ) continue ;

                    $detect = array_diff( $this->possible[$x][$y], $this->getRow($x, $y ) ) ;
                    if ( count($detect) == 1 )
                    {
                         $this->data[$x][$y] = array_shift($detect); 
                         $this->log($x,$y, $this->data[$x][$y], "Deduction ( Row )"); 
                         return true;
                    }

                    $detect = array_diff( $this->possible[$x][$y], $this->getCol($x, $y ) ) ;
                    if ( count($detect) == 1 )
                    {
                         $this->data[$x][$y] = array_shift($detect); 
                         $this->log($x,$y, $this->data[$x][$y], "Deduction ( Col )"); 
                         return true;

                    }

                    $detect = array_diff( $this->possible[$x][$y], $this->getBox($x, $y ) ) ;
                    if ( count($detect) == 1 )
                    {
                         $this->data[$x][$y] = array_shift($detect); 
                         $this->log($x,$y, $this->data[$x][$y], "Deduction ( Box )"); 
                         return true;

                    }
               }
          }
     }/*}}}*/

     private function getRow( $x1, $y  )/*{{{*/
     {
          $numbers = array(); 
          for($x = 0 ; $x < 9; $x++ )
          {
               if ( $x == $x1 ) continue ;
               if ( $this->data[$x][$y] !== null ) continue ;
               foreach ( $this->possible[$x][$y] as $num )
                    if ( !in_array($num, $numbers ))
                         $numbers[] = $num ;
               
          }
          return $numbers ;
     }/*}}}*/
     private function getCol( $x, $y1  )/*{{{*/
     {
          $numbers = array(); 
          for($y = 0 ; $y < 9; $y++ )
          {
               if ( $y == $y1 ) continue ;
               if ( $this->data[$x][$y] !== null ) continue ;
               foreach ( $this->possible[$x][$y] as $num )
                    if ( !in_array($num, $numbers ))
                         $numbers[] = $num ;
               
          }
          return $numbers ;
     }/*}}}*/

     private function getBox( $x1, $y1  )/*{{{*/
     {
          if ( $x1 != 1 || $y1 != 7 )
               return array() ;

          $numbers = array(); 

          $x = ( (int) ( $x1 / 3 ) ) * 3 ;
          $y = ( (int) ( $y1 / 3 ) ) * 3 ;

          for ( $y3 = 0; $y3 < 3; $y3++ )
               for ( $x3 = 0; $x3 < 3; $x3++ )
               {
                    $y2 = $y + $y3; 
                    $x2 = $x + $x3; 

                    if ( $y2 == $y1 && $x2 == $x1 ) continue ;
                    if ( $this->data[$x2][$y2] !== null ) continue ;
                    foreach ( $this->possible[$x2][$y2] as $num )
                         if ( !in_array($num, $numbers ))
                              $numbers[] = $num ;
               
          }

          return $numbers ;
     }/*}}}*/

     private function log($x, $y, $value, $why )/*{{{*/
     {
          if ( $this->quiet )  return ;
          if ($x == -1 )
               ; //echo "<b>$why</b> <br />";
          else
          {
               echo "$x,$y => $value ( $why ) <br />"; 
               if ( !array_key_exists($why, $this->log ))
                    $this->log[$why] = 0;
               $this->log[$why]++; 
          }
          flush(); 
     }/*}}}*/
     public function __destruct()/*{{{*/
     {
          return ;
          foreach ( $this->log  as $key => $value )
               echo "<b>$key</b> => $value <br />";
     }/*}}}*/

     private function isValid($x, $y, $a )/*{{{*/
     {
          return 
               !$this->inRow($y,$a)
          && !$this->inCol($x, $a )
        && !$this->inBox($x, $y, $a )
               ;
     }/*}}}*/
     private function inRow( $y, $a )/*{{{*/
     {
          for($i = 0 ; $i < 9; $i++ )
          {
               if ( $this->data[$i][$y] == $a )
                    return true; 
          }
          return false; 
     }/*}}}*/
     private function inCol( $x, $a )/*{{{*/
     {
          for($i = 0 ; $i < 9; $i++ )
          {
               if ( $this->data[$x][$i] == $a )
                    return true; 
          }
          return false; 
     }/*}}}*/
     private function inBox( $x,$y, $a )/*{{{*/
     {
          $x = ( (int) ( $x / 3 ) ) * 3 ;
          $y = ( (int) ( $y / 3 ) ) * 3 ;

          for ( $y1 = 0; $y1 < 3; $y1++ )
               for ( $x1 = 0; $x1 < 3; $x1++ )
               {
                    $y2 = $y + $y1; 
                    $x2 = $x + $x1; 
                    if ( $this->data[$x2][$y2] == $a )
                         return true ;
               }
          return false; 
     }/*}}}*/

}

//$s = new Sudoku ( $puzzle['medium'] );
//$s = new Sudoku ( $puzzle['easy'] );
//$s = new Sudoku ( $puzzle['hard'] );
//$s = new Sudoku ( $puzzle['vhard'] );

set_time_limit( 10 );
$s = new Sudoku ( $puzzle['hardest'] );

echo '<div style="float:left;">';
$s->solve(); 
echo '</div>';
echo '<div style="float:left;">';
$s->display(); 
echo '</div>';


?>
