html
  body
    div. 
      Okays Multiline
      some text

    @forelse($.server as $k=>$item)
    <div>{{ $k }} -> {{ $item }}</div>
    @empty
      :(
    @endforelse

    @php
      // hallo bitches

      class X {
        /*
          multiline comments
          lol okay sasai
        */

        public function __construct(){
          // return
        }
      }
    @endphp

    style.
      body {
        color: red;
      }

    script.
    	if(true){
    		console.log('Jade!')
    	}