<style>
    .board {
        display: flex;
        gap: 20px;
        padding: 20px;
        background: #fff;
        flex-wrap: no-wrap;
        width: 151%;
    }

    .skolling{
        overflow-x: scroll;
    }

    .column {
        box-sizing: border-box;
        width: calc(25% - 15px);
        background: #fff;
        padding: 15px;
        /*min-height: 300px;*/
        height: 100%;
        box-shadow: unset!important;
        border-radius: 4px !important;
        border: 1px solid #E2E2E2!important;
        overflow: hidden;
    }

    .column h3 {
        text-align: center;
        margin-bottom: 10px;
    }

    .card {
        background: #e0e0e0;
        margin: 10px 0;
        padding: 10px;
        border-radius: 8px;
        cursor: grab;
        transition: background 0.2s;
    }

    .card:hover {
        background: #d5d5d5;
    }

    .drag-header h3 {
        margin: 0px;
        border: 1px solid #2C3D67;
        background: #2C3D67;
        font-size: 14px;
        font-weight: 600;
        line-height: 12px;
        text-transform: uppercase;
        letter-spacing: 0.25px;
        border-radius: 50px;
        padding: 3px 15px;
        display: inline-flex;
    }

    .apprtunity-price {
        color: #242424;
        font-size: 13px;
        font-weight: 400;
        line-height: 16px;
        letter-spacing: 0.5px;
        margin-top: 15px;
    }

    .flex.anulprice p {
        margin: 0px;
    }

    p.same {
    color: #5E5E5E;
    font-size: 13px;
    font-weight: 600;
    line-height: 14px;
    letter-spacing: 0.5px;
    margin-top: 8px;
    width: 135px;
}

p.dam {
    background: #E5F3FF;
    border-radius: 50px;
    color: #2E2E2E;
    font-size: 15px !important;
    font-weight: 500 !important;
    line-height: 14px;
    padding: 5px 5px;
    width: 87px !important;
    height: 36px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
}

    .flex.anulprice {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }

    .card_drag .card-header {
        border-bottom: 1px solid #E8E8E8 !important;
    }

    .card_drag .card-header p {
        color: #4B4B4B;
        font-size: 14px;
        font-weight: 600;
        line-height: 12px;
        margin-bottom: 0px;
        letter-spacing: 0px;
    }

    .img_dd img {
        margin-right: 10px;
        border-radius: 50px;
    }

    .card_drag .card-header .header_image img {
        width: 25px;
        height: 25px;
        object-fit: cover;
        margin-right: 9px;
    }

  /*  .card-body.bgrid.column_drag {
        padding: .9375rem 12px!important;
    }
*/
    .card_drag .card-body {
    padding: .9375rem 6px;
}


    .card_drag .data-ss p {
        margin: 0px;
        color: #5E5E5E;
        font-size: 12px;
        font-weight: 400;
        line-height: 12px;
        letter-spacing: 0px;
    }

    .column_drag
     {
        min-height: 500px;
    }

    .bgrid {
        background: #F2F2F4;
    }

    .card_drag .data-ss h5 {
        font-size: 14px;
        color: #2E2E2E;
        line-height: 14px;
        margin-bottom: 2px;
        font-weight: 600;
        letter-spacing: 0px;
    }

    button.hoverbtn {
        width: 20px;
        height: 20px;
        background: transparent!important;
        box-shadow: unset;
        padding: 0px;
        position: absolute;
        top: -5px;
        right: 1px;
        opacity: 0;
    }

    button.hoverbtn img {
        width: 17px;
        height: auto;
    }

    .card_drag .card-header {
        position: relative;
    }

.card_drag .card-header:hover button.hoverbtn {opacity: 1;}

@media (max-width: 767px){

 .board{
    flex-direction:column;
 }

.column_drag {
    min-height: auto;
 }
}
</style>
<div>
    <div class="row text-end mb-3">
        <div class="col">
          <?php
            $no_show_sum = $no_show_opportunities->sum('amount')??0;
            $demo_book_sum = $demo_book_opportunities->sum('amount')??0;
            $demo_completed_sum = $demo_completed_opportunities->sum('amount')??0;
            $negotiating_sum = $negotiating_opportunities->sum('amount')??0;
            $interested_sum = $interested_opportunities->sum('amount')??0;
            $not_interested_sum = $not_interested_opportunities->sum('amount')??0;
          ?>
          <!-- <h5>Total Annualised Value: ₹ <strong>{{$no_show_sum+$demo_book_sum+$demo_completed_sum+$negotiating_sum}}  </strong></h5> -->
        </div>
    </div>
    <div class="skolling">
          <div class="board">
              <div class="column p-0" >
                 <div class="card p-0 m-0">
                     <div class="card-header bg-white drag-header">
                        <h3>No Show</h3>
                        <div class="apprtunity-price"> {{count($no_show_opportunities)}} OPPORTUNITY</div>
                        <div class="flex anulprice">
                         <p class="same"> Annualized Value</p>
                         <p class="dam">₹ {{$no_show_sum}}</p>
                       </div>
                     </div>
                     <div class="card-body bgrid column_drag" data-status="no_show">
                         @foreach($no_show_opportunities  as $no_show_opportunity)
                          <div class="card_drag card bg-white p-0" draggable="true" data-id="{{$no_show_opportunity->id}}">
                            <div class="card-header p-2">
                                <div class="d-flex flex-row justify-content-start align-items-center">
                                     <div class="header_image">
                                        <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="text">
                                        <p>{{$no_show_opportunity->note}}</p>
                                     </div>
                                </div>
                                 <button type="button" class="hoverbtn btn" onclick="getOpportunitydata('{{$no_show_opportunity->id}}')"> <img src="{{ url('/').'/'.asset('assets/img')}}/ph_note-pencil-fill.svg"></button>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                     <div class="img_dd">
                                         <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="data-ss">
                                         <h5>₹{{$no_show_opportunity->amount}}</h5>
                                         <p>{{$no_show_opportunity->confidence}}% on {{date("d/m/Y",strtotime($no_show_opportunity->estimated_close_date))}}</p>
                                         
                                     </div>

                                </div>
                            </div>
                           
                           
                          </div>
                         @endforeach
                     </div>
                 </div>
              </div>

              <div class="column p-0" >
                 <div class="card p-0 m-0">
                     <div class="card-header bg-white drag-header">
                        <h3>Demo Book</h3>
                        <div class="apprtunity-price"> {{count($demo_book_opportunities)}} OPPORTUNITY</div>
                        <div class="flex anulprice">
                         <p class="same"> Annualized Value</p>
                         <p class="dam">₹ {{$demo_book_sum}}</p>
                       </div>
                     </div>
                     <div class="card-body bgrid column_drag" data-status="demo_book">
                         @foreach($demo_book_opportunities  as $demo_book_opportunity)
                          <div class="card_drag card bg-white p-0" draggable="true" data-id="{{$demo_book_opportunity->id}}">
                            <div class="card-header p-2">
                                <div class="d-flex flex-row justify-content-start align-items-center">
                                     <div class="header_image">
                                        <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="text">
                                        <p>{{$demo_book_opportunity->note}}</p>
                                     </div>
                                </div>
                                 <button type="button" class="hoverbtn btn" onclick="getOpportunitydata('{{$demo_book_opportunity->id}}')"> <img src="{{ url('/').'/'.asset('assets/img')}}/ph_note-pencil-fill.svg"></button>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                     <div class="img_dd">
                                         <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="data-ss">
                                         <h5>₹{{$demo_book_opportunity->amount}}</h5>
                                         <p>{{$demo_book_opportunity->confidence}}% on {{date("d/m/Y",strtotime($demo_book_opportunity->estimated_close_date))}}</p>
                                     </div>
                                </div>
                            </div>
                          
                           
                          </div>
                         @endforeach
                     </div>
                 </div>
              </div>

              <div class="column p-0">
                 <div class="card p-0 m-0">
                     <div class="card-header bg-white drag-header">
                        <h3>Demo Completed</h3>
                        <div class="apprtunity-price"> {{count($demo_completed_opportunities)}} OPPORTUNITY</div>
                        <div class="flex anulprice">
                         <p class="same"> Annualized Value</p>
                         <p class="dam">₹ {{$demo_completed_sum}}</p>
                       </div>
                     </div>
                     <div class="card-body bgrid column_drag" data-status="demo_completed">
                         @foreach($demo_completed_opportunities  as $demo_completed_opportunity)
                          <div class="card_drag card bg-white p-0" draggable="true" data-id="{{$demo_completed_opportunity->id}}">
                            <div class="card-header p-2">
                                <div class="d-flex flex-row justify-content-start align-items-center">
                                     <div class="header_image">
                                        <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="text">
                                        <p>{{$demo_completed_opportunity->note}}</p>
                                     </div>
                                </div>
                                 <button type="button" class="hoverbtn btn" onclick="getOpportunitydata('{{$demo_completed_opportunity->id}}')"> <img src="{{ url('/').'/'.asset('assets/img')}}/ph_note-pencil-fill.svg"></button>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                     <div class="img_dd">
                                         <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="data-ss">
                                         <h5>₹{{$demo_completed_opportunity->amount}}</h5>
                                         <p>{{$demo_completed_opportunity->confidence}}% on {{date("d/m/Y",strtotime($demo_completed_opportunity->estimated_close_date))}}</p>
                                     </div>
                                </div>
                            </div>
                          
                           
                          </div>
                         @endforeach
                     </div>
                 </div>
              </div>

               <div class="column p-0">
                 <div class="card p-0 m-0">
                     <div class="card-header bg-white drag-header">
                        <h3>Negotiating</h3>
                        <div class="apprtunity-price"> {{count($negotiating_opportunities)}} OPPORTUNITY</div>
                        <div class="flex anulprice">
                         <p class="same"> Annualized Value</p>
                         <p class="dam">₹ {{$negotiating_sum}}</p>
                       </div>
                     </div>
                     <div class="card-body bgrid column_drag" data-status="negotiating">
                         @foreach($negotiating_opportunities  as $negotiating_opportunity)
                          <div class="card_drag card bg-white p-0" draggable="true" data-id="{{$negotiating_opportunity->id}}">
                            <div class="card-header p-2">
                                <div class="d-flex flex-row justify-content-start align-items-center">
                                     <div class="header_image">
                                        <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="text">
                                        <p>{{$negotiating_opportunity->note}}</p>
                                     </div>
                                </div>
                                 <button type="button" class="hoverbtn btn" onclick="getOpportunitydata('{{$negotiating_opportunity->id}}')"> <img src="{{ url('/').'/'.asset('assets/img')}}/ph_note-pencil-fill.svg"></button>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                     <div class="img_dd">
                                         <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="data-ss">
                                         <h5>₹{{$negotiating_opportunity->amount}}</h5>
                                         <p>{{$negotiating_opportunity->confidence}}% on {{date("d/m/Y",strtotime($negotiating_opportunity->estimated_close_date))}}</p>
                                     </div>
                                </div>
                            </div>
                          
                           
                          </div>
                         @endforeach
                     </div>
                 </div>
              </div>

              <div class="column p-0">
                 <div class="card p-0 m-0">
                     <div class="card-header bg-white drag-header">
                        <h3>Interested</h3>
                        <div class="apprtunity-price"> {{count($interested_opportunities)}} OPPORTUNITY</div>
                        <div class="flex anulprice">
                         <p class="same"> Annualized Value</p>
                         <p class="dam">₹ {{$interested_sum}}</p>
                       </div>
                     </div>
                     <div class="card-body bgrid column_drag" data-status="interested">
                         @foreach($interested_opportunities  as $interested_opportunity)
                          <div class="card_drag card bg-white p-0" draggable="true" data-id="{{$interested_opportunity->id}}">
                            <div class="card-header p-2">
                                <div class="d-flex flex-row justify-content-start align-items-center">
                                     <div class="header_image">
                                        <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="text">
                                        <p>{{$interested_opportunity->note}}</p>
                                     </div>
                                </div>
                                 <button type="button" class="hoverbtn btn" onclick="getOpportunitydata('{{$interested_opportunity->id}}')"> <img src="{{ url('/').'/'.asset('assets/img')}}/ph_note-pencil-fill.svg"></button>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                     <div class="img_dd">
                                         <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="data-ss">
                                         <h5>₹{{$interested_opportunity->amount}}</h5>
                                         <p>{{$interested_opportunity->confidence}}% on {{date("d/m/Y",strtotime($interested_opportunity->estimated_close_date))}}</p>
                                     </div>
                                </div>
                            </div>
                          
                          </div>
                         @endforeach
                     </div>
                 </div>
              </div>

                <div class="column p-0">
                 <div class="card p-0 m-0">
                     <div class="card-header bg-white drag-header">
                        <h3>Not Interested</h3>
                        <div class="apprtunity-price"> {{count($not_interested_opportunities)}} OPPORTUNITY</div>
                        <div class="flex anulprice">
                         <p class="same"> Annualized Value</p>
                         <p class="dam">₹ {{$not_interested_sum}}</p>
                       </div>
                     </div>
                     <div class="card-body bgrid column_drag" data-status="not_interested">
                         @foreach($not_interested_opportunities  as $not_interested_opportunity)
                          <div class="card_drag card bg-white p-0" draggable="true" data-id="{{$not_interested_opportunity->id}}">
                            <div class="card-header p-2">
                                <div class="d-flex flex-row justify-content-start align-items-center">
                                     <div class="header_image">
                                        <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="text">
                                        <p>{{$not_interested_opportunity->note}}</p>
                                     </div>
                                </div>
                                 <button type="button" class="hoverbtn btn" onclick="getOpportunitydata('{{$not_interested_opportunity->id}}')"> <img src="{{ url('/').'/'.asset('assets/img')}}/ph_note-pencil-fill.svg"></button>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                     <div class="img_dd">
                                         <img src="{{ url('/').'/'.asset('assets/img')}}/circaldrag.svg">
                                     </div>
                                     <div class="data-ss">
                                         <h5>₹{{$not_interested_opportunity->amount}}</h5>
                                         <p>{{$not_interested_opportunity->confidence}}% on {{date("d/m/Y",strtotime($not_interested_opportunity->estimated_close_date))}}</p>
                                     </div>
                                </div>
                            </div>
                          
                          </div>
                         @endforeach
                     </div>
                 </div>
              </div>
             

               
          </div>
          </div>
      </div>
</div>
<script>
    var draggedCard = null;

    document.querySelectorAll('.card_drag').forEach(card => {
        card.addEventListener('dragstart', () => {
            draggedCard = card;
            card.style.opacity = '0.5';
        });

        card.addEventListener('dragend', () => {
            draggedCard.style.opacity = '1';
            draggedCard = null;
        });
    });

    document.querySelectorAll('.column_drag').forEach(column => {
        column.addEventListener('dragover', (e) => {
            e.preventDefault();
        });

        column.addEventListener('drop', () => {
            if (draggedCard) {
                column.appendChild(draggedCard);

                const card_id = draggedCard.getAttribute('data-id');
                const new_status = column.getAttribute('data-status');

                //console.log(`Card ID: ${card_id}, New Status: ${newStatus}`);

                $.post("{{ route('lead-opportunities.updateCardStatus') }}", {card_id:card_id,new_status:new_status }, function(response){
                    getCardData();
                }); 

               
            }
        });
    });
</script>