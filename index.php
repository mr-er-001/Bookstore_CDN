
<?php include'topheader.php' ?>
  <?php include 'dbb.php' ?>
<style>
.custom-card {
  /*width: 416px;*/
  height: 150px;
}
</style>

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row">
        

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">
<div class="col-xxl-4 col-md-4">
  <div class="card shadow-sm p-3 d-flex flex-row align-items-center justify-content-between custom-card">
    <!-- Left side text -->
    <div style="margin-left: 0.5cm">
        <h4 class="text-muted mb-1" style="color: #045E70 !important;">Total Sales</h4>
        <h3 class="fw-bold" style="color: #045E70 !important;">
        <?php
          $sale = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total_sales FROM sale_invoice"));
          echo $sale['total_sales']; 
        ?>
      </h3>
    </div>
    <!-- Right side icon -->
    <div>
      <img src="assets/img/sale.png" alt="Sales" style="margin-right: 50px;width: 90px">
    </div>
  </div>
</div><!-- End Sales Card -->



<div class="col-xxl-4 col-md-4">
  <div class="card shadow-sm p-3 d-flex flex-row align-items-center justify-content-between custom-card">
    <div style="margin-left: 0.5cm">
      <h4 class="text-muted mb-1" style="color: #045E70 !important;">Total Purchases</h4>
      <h3 class="fw-bold" style="color: #045E70 !important;">
        <?php
          $purchases = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total_purchases FROM purchase_invoice"));
          echo $purchases['total_purchases'];
        ?>
      </h3>
    </div>
    <div>
      <img src="assets/img/purchase.png" alt="Purchases"style="margin-right: 50px;width: 90px">
    </div>
  </div>
</div>
<div class="col-xxl-4 col-md-4">
  <div class="card shadow-sm p-3 d-flex flex-row align-items-center justify-content-between custom-card">
    <div style="margin-left: 0.5cm">
      <h4 class="text-muted mb-1" style="color: #045E70 !important;">Total Books</h4>
      <h3 class="fw-bold" style="color: #045E70 !important;">
        <?php
          $t_book = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total_books FROM books"));
          echo $t_book['total_books'];
        ?>
      </h3>
    </div>
    <div>
      <img src="assets/img/books.png" alt="Books" width="60" style="margin-right: 50px;width: 90px">
    </div>
  </div>
</div><!-- End Books Card -->



            
            


            

            <!-- Reports -->
            <div class="col-12">
              <div class="card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Reports <span>/Today</span></h5>

                  <!-- Line Chart -->
                  <div id="reportsChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                          name: 'Sales',
                          data: [31, 40, 28, 51, 42, 82, 56],
                        }, {
                          name: 'Revenue',
                          data: [11, 32, 45, 32, 34, 52, 41]
                        }, {
                          name: 'Customers',
                          data: [15, 11, 32, 18, 9, 24, 11]
                        }],
                        chart: {
                          height: 350,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                        },
                        markers: {
                          size: 4
                        },
                        colors: ['#4154f1', '#2eca6a', '#ff771d'],
                        fill: {
                          type: "gradient",
                          gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.4,
                            stops: [0, 90, 100]
                          }
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2
                        },
                        xaxis: {
                          type: 'datetime',
                          categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
                        },
                        tooltip: {
                          x: {
                            format: 'dd/MM/yy HH:mm'
                          },
                        }
                      }).render();
                    });
                  </script>
                  <!-- End Line Chart -->

                </div>

              </div>
            </div><!-- End Reports -->

            <!-- Recent Sales -->
           
          </div>
        </div><!-- End Left side columns -->

      
    </section>

  </main><!-- End #main -->

    <?php include'footer.php' ?>