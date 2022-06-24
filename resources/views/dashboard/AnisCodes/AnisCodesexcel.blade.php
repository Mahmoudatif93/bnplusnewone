
   <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                              
                                <th>Client Name</th>
                              <th>Client Number</th>
                                <th>Order Id</th>
                                 <th>Card Name</th>
                                <th>@lang('site.card_code')</th>
                                <th>Payment Type</th>
                                 <th>Created At</th>
                                
                                
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($Cards as $index=>$category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    
                                    <td>{{ $category->orders->client_name }}</td>
                                    <td>{{ $category->orders->client_number }}</td>
                                    <td>{{ $category->order_id }}</td>
                                    <?php $cardnames=\App\Cards::where(['id' => $category->orders->card_id])->first();?>
                                <td>{{ $cardnames->card_name }}</td>
                                    
                                     <td>{{ $category->card_code }}</td>
                                    <td>{{ $category->orders->paymenttype }}</td>
                                    <td>{{ $category->orders->created_at }}</td>
                                    
                                 

                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->