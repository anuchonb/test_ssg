
    </div><!-- /.container-fluid -->
</div><!-- /.main-content -->
<footer class="footer mt-auto py-3 bg-light" style="margin-left: 250px; transition: all 0.3s;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-muted">
                    &copy; <?php echo date('Y'); ?> CRM Condo System v1.0 | 
                    พัฒนาโดย ChonieDev | 
                    เวอร์ชั่นล่าสุด: <?php echo date('d/m/Y H:i'); ?>
                </small>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-muted">
                    <i class="fas fa-server"></i> 
                    <?php 
                        if(isset($_SESSION['user_name'])) {
                            echo 'ผู้ใช้: ' . htmlspecialchars($_SESSION['user_name']) . ' | ';
                        }
                    ?>
                    <span id="serverTime"></span>
                </small>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/th.js"></script>
<script>
const BASE_URL = '<?php echo dirname($_SERVER['PHP_SELF']); ?>';
const API_URL = '../api/';
const CURRENT_PAGE = '<?php echo basename($_SERVER['PHP_SELF']); ?>';
</script>
<script src="../assets/js/common.js"></script>
</body>
</html>