@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>Products Orders</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">Products Orders</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.products') <small>{{count($orders)}}</small></h3>

                 <!--   <form action="{{ route('dashboard.productsorders.index') }}" method="get">

                        <div class="row">

                        

                            <div class="col-md-4">
                                @if (auth()->user()->hasPermission('create_Cards'))
                                    <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                            </div>

                        </div>
                    </form> end of form -->

               
                   
                </div><!-- end of box header -->

                <div class="box-body">

                    @if (isset($orders) > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                 <th>Client Name</th>
                                  <th>Client Phone</th>
                                   <th>Client Email</th>
                                <th>Order Id</th>
                               
                                <th>Price</th>
                                <th>product</th>
                                <th>quantity</th>
                            
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($orders as $index=>$category)
                                <tr>
                                 
                                    
                                  <?php  $users=DB::connection('products')->select("select * from userdata where userid='$category->userid'");?>

                                        <td>
                                        @if(!empty($users[0]))
                                        {{ $users[0]->first_name . '' . $users[0]->last_name }}
                                        @else
                                        لا يوجد
                                        @endif
                                        </td>
                                        
                                              <td>
                                        @if(!empty($users[0]))
                                        {{ $users[0]->phone  }}
                                        @else
                                        لا يوجد
                                        @endif
                                        </td>
                                        
                                           <td>
                                        @if(!empty($users[0]))
                                        {{ $users[0]->email  }}
                                        @else
                                        لا يوجد
                                        @endif
                                        </td>
                                        
                                    <td>{{ $category->orderid }}</td>
                                    <td>{{ $category->price }}</td>
                                    <?php  $product=DB::connection('products')->select("select * from product where id='$category->productid'");?>
                                   
                                    <td>
                                        @if(!empty($product[0]))
                                        {{ $product[0]->name }}
                                        @else
                                        لا يوجد
                                        @endif
                                        </td>
                                    <td>{{ $category->quantity }}</td>
                           


                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

                        {{-- $Cards->appends(request()->query())->links() --}}

                    @else

                        <h2>@lang('site.no_data_found')</h2>

                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->



    <script type="text/javascript">
    $(document).ready(function() {
        $('select[name="company_id"]').on('change', function() {
            var company_id = $(this).val();
            if(company_id) {
                $.ajax({
                    url: 'compcard/'+company_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {

                        
                        $('select[name="card_price"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="card_price"]').append('<option value="'+ value +'">'+ value +'</option>');
                        });


                    }
                });
            }else{
                $('select[name="card_price"]').empty();
            }
        });
    });
</script>



@endsection
