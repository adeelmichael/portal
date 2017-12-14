						<div class="container" style="margin-top: 20px;">
      <div class="row">
     
        <div class="col-md-12" >
   
            <?php foreach($profiles as $data){ ?>
          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title" style="text-align: center; font-size: 20px; font-weight: normal;"><?php echo ucfirst($data['f_name']); ?>&nbsp;<?php echo ucfirst($data['l_name']); ?></h3>
            </div>
            <div class="panel-body">
              <div class="row">
                  <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="<?php echo base_url(); ?>assets/images/user.png" class="img-circle img-responsive"> </div>
             
                <div class=" col-md-9 col-lg-9 "> 
                  <table class="table table-user-information">
                    <tbody>
                        <tr>
                        <td>Town</td>
                        <td><?php echo $data['town']; ?></td>
                      </tr>
                      
                       <tr>
                        <td>Post Code</td>
                        <td><?php echo $data['postCode']; ?></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td><?php echo $data['email']; ?></td>
                      </tr>
                      
                       <tr>
                        <td>Phone</td>
                        <td><?php echo $data['phone']; ?></td>
                           
                      </tr>
                      <tr>
                      <td>Business Address</td>
                        <td><?php echo $data['business_address']; ?></td>
                      </tr>
                     <tr>
                      <td>Distance Willing to Travel</td>
                        <td><?php echo $data['distance']; ?></td>
                      </tr>
                      <tr>
                     <td>Business Type</td>
                        <td><?php echo $data['business_type']; ?></td>
                      </tr>
                      <tr>
                      <td>Details about me</td>
                        <td><?php echo $data['details']; ?></td>
                      </tr>
                      
                      <tr>
                      <td>Primary Trade</td>
                        <td><?php echo $data['primary_trade']; ?></td>
                      </tr>
                      <?php foreach($meta as $mt){ ?>
                      <tr>
                      <td>Uploads</td>
                      <td><img src="<?php echo base_url(); ?><?php echo $mt['media']; ?>" width="300" height="300"></td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                  
               
                </div>
              </div>
            </div>
                 <div class="panel-footer">
                        
                    </div>
            
          </div>
             <?php } ?>
           
        </div>
      </div>
    </div>