<!DOCTYPE html>
 <html>
    <head>
         <title>this is my mater page</title>
         <style></style>
         <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
         <article>
             <header>
                    <h1>this is my master page</h1>
             </header>
             <main>
                 <button id='myajax'>click me</button>
             </main>
             <footer>
 </footer>
         </article>
       {{--all my scripts goes here--}}
       <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
       <script type = "text/javascript">
         $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
         });
         $('#myajax').click(function(){
            //we will send data and recive data fom our AjaxController
            //alert("im just clicked click me");
            $.ajax({
               url:'miJqueryAjax',
               data:{'name':"luis"},
               type:'post',
               success:  function (response) {
                  alert(response);
               },
               statusCode: {
                  404: function() {
                     alert('web not found');
                  }
               },
               error:function(x,xs,xt){
                  window.open(JSON.stringify(x));
                  //alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
               }
            });
             });
       </script>
    </body>
 </html>