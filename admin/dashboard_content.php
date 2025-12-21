<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: " . BASE_URL . "login.php");
  exit;
}



/* Notices */
$total_notices = $conn->query("SELECT COUNT(*) total FROM notices")
  ->fetch_assoc()['total'];

$published = $conn->query("SELECT COUNT(*) total FROM notices WHERE status='Published'")
  ->fetch_assoc()['total'];

$draft = $conn->query("SELECT COUNT(*) total FROM notices WHERE status='Draft'")
  ->fetch_assoc()['total'];

$total_categories = $conn->query("SELECT COUNT(DISTINCT category) total FROM notices")->fetch_assoc()['total'];

/* Users */
$total_users = $conn->query("SELECT COUNT(*) total FROM users")->fetch_assoc()['total'];

$students = $conn->query("SELECT COUNT(*) total FROM users WHERE role='student'")->fetch_assoc()['total'];

$teachers = $conn->query("SELECT COUNT(*) total FROM users WHERE role='teacher'")->fetch_assoc()['total'];

/* PIE CHART DATA */
$labels = [];
$data   = [];
$result = $conn->query("SELECT category, COUNT(*) total FROM notices GROUP BY category");
while ($row = $result->fetch_assoc()) {
  $labels[] = $row['category'];
  $data[]   = $row['total'];
}
?>

<div class="row  pl5">
  <div class="col-xl-3 col-lg-6">
    <div class="card">
      <div class="card-body card-type-3">
        <div class="row">
          <div class="col">
            <h5 class="text-muted mb-0">Total Notices</h5>
            <h6 class="font-weight-bold mb-0"><?= $total_notices ?></h6>
          </div>
          <div class="col-auto">
            <div class="card-circle l-bg-green text-white">
              <i class="fas fa-clipboard-list"></i>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="col-xl-3 col-lg-6">
    <div class="card">
      <div class="card-body card-type-3">
        <div class="row">
          <div class="col">
            <h5 class="text-muted mb-0">Published Notices</h5>
            <h6 class="font-weight-bold mb-0"><?= $published ?></h6>
          </div>
          <div class="col-auto">
            <div class="card-circle l-bg-purple text-white">
              <i class="fas  fa-check-circle"></i>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-6">
    <div class="card">
      <div class="card-body card-type-3">
        <div class="row">
          <div class="col">
            <h5 class="text-muted mb-0">Draft Notices</h5>
            <h6 class="font-weight-bold mb-0"><?= $draft ?></h6>
          </div>
          <div class="col-auto">
            <div class="card-circle l-bg-orange text-white">
              <i class="fas fa-file-alt"></i>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- categories -->
  <div class="col-xl-3 col-lg-7">
    <div class="card">
      <div class="card-body card-type-3">
        <div class="row">
          <div class="col">
            <h5 class="text-muted mb-0">Total Categories</h5>
            <h6 class="font-weight-bold mb-0"><?= $total_categories ?></h6>
          </div>
          <div class="col-auto">
            <div class="card-circle l-bg-yellow text-white">
              <i class="fas fa-tags"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- User -->
  <div class="col-xl-3 col-lg-6">
    <div class="card">
      <div class="card-body card-type-3">
        <div class="row">
          <div class="col">
            <h5 class="text-muted mb-0">Users</h5>
            <h6 class="font-weight-bold mb-0"><?= $total_users ?></h6>
          </div>
          <div class="col-auto">
            <div class="card-circle l-bg-green text-white">
              <i class="fas fa-user"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Teachers -->
  <div class="col-xl-3 col-lg-6">
    <div class="card">
      <div class="card-body card-type-3">
        <div class="row">
          <div class="col">
            <h5 class="text-muted mb-0">Teachers</h5>
            <h6 class="font-weight-bold mb-0"><?= $teachers ?></h6>
          </div>
          <div class="col-auto">
            <div class="card-circle l-bg-red text-white">
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



  <!-- Teachers -->
  <div class="col-xl-3 col-lg-6">
    <div class="card">
      <div class="card-body card-type-3">
        <div class="row">
          <div class="col">
            <h5 class="text-muted mb-0">Students</h5>
            <h6 class="font-weight-bold mb-0"><?= $students ?></h6>
          </div>
          <div class="col-auto">
            <div class="card-circle l-bg-orange  text-white">
              <i class="fas fa-user-graduate"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


</div>

<div class="col-12 col-md-6 col-lg-6 mx-auto">
  <div class="card">
    <div class="card-header">
      <h4>Pie Chart</h4>
    </div>
    <div class="card-body">
      <canvas id="myChart4"></canvas>
    </div>
  </div>
</div>
</div>
<div class="card-body"></div>



<script>
  window.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('myChart4').getContext('2d');
    const myChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: <?= json_encode($labels) ?>, // dynamic labels from PHP
        datasets: [{
          data: <?= json_encode($data) ?>, // dynamic data from PHP
          backgroundColor: [
            '#FF6384', '#36A2EB', '#FFCE56',
            '#4BC0C0', '#9966FF', '#FF9F40'
          ],
          borderColor: '#fff', // adds white borders between slices
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'right', // move legend to the right
            labels: {
              font: {
                size: 14,
                weight: 'bold'
              },
              color: '#333'
            }
          },
          tooltip: {
            enabled: true,
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const value = context.raw;
                const percentage = ((value / total) * 100).toFixed(1);
                return `${context.label}: ${value} (${percentage}%)`;
              }
            }
          },
          title: {
            display: true,
            text: 'Notices Distribution by Category',
            font: {
              size: 18,
              weight: 'bold'
            },
            color: '#444'
          }
        }
      }
    });
  });
</script>