



<!DOCTYPE html>
<html lang="en">
<head>
  <title>أستكمال عمليه الدفع</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="background-color:#c7c7c7 ">



        
        
        
    <header class="main-header" style="text-align:center">

            <span class="logo-mini"><b></b></span>
            <span class="logo-lg"><h3     style="color: #1000ff;">BN plus </h3></span>
        </a>

        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
    

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    


                   
                </ul>
            </div>
        </nav>

    </header>




<div class="container" style="text-align:center">
  
  <?php 
 $details= \App\tdawalDetails::where(['id' => request()->id])->first();
  
  ?>
  @if(!empty($details))
  <h3>مرحبا <span style="color:red">
      
       {{$details->client_name}}
      
  </span> 
  
  </h3>


<h5>برجاء أستكمال عمليه الدفع</h5>
<div>  <a type="button" class="btn btn-primary mt-5" href="{{ $details->url}}">أستكمال</a> 
</div>
@else

<h3>مرحبا <span style="color:red">
      
     لا يوجد عمليه 
      برجاء المحاوله مره اخري
  </span> 
  
  </h3>
@endif


  

</div>

</body>
</html>