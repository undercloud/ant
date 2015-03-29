!!! 5
html
  head
    title(name="oj") {{ date('Y-m-d') }}
  body Hello Multiline text
    @php
      class X {
        public function __construct(){
          echo __DIR__;
        }
      }

      new X();
    @endphp

    div(
      class="x",
      id="y"
    ) divline text

    style.
      body {
        color: red;
      }