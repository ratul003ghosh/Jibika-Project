<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../admin_login.php");
    exit();
}

include('../assets/config/db.php');

$lang = $_SESSION['lang'] ?? 'bn';

$auText = [
    'bn' => [
        'page_title'      => 'ব্যবহারকারী পরিচালনা — জীবিকা পোর্টাল',
        'title'           => 'ব্যবহারকারী পরিচালনা',
        'subtitle'        => 'প্ল্যাটফর্মে নিবন্ধিত সকল ব্যবহারকারী পর্যালোচনা ও পরিচালনা করুন।',
        'back_btn'        => 'ড্যাশবোর্ডে ফিরুন',
        'search_ph'       => 'নাম বা ইমেইল অনুসন্ধান করুন...',
        'total_users'     => 'মোট ব্যবহারকারী',
        'filter_all'      => 'সবাই',
        'filter_seeker'   => 'চাকরিপ্রার্থী',
        'filter_employer' => 'নিয়োগকর্তা',
        'filter_admin'    => 'অ্যাডমিন',
        'th_serial'       => '#',
        'th_name'         => 'নাম',
        'th_email'        => 'ইমেইল',
        'th_phone'        => 'ফোন',
        'th_role'         => 'ভূমিকা',
        'th_created'      => 'যোগদানের তারিখ',
        'th_action'       => 'পদক্ষেপ',
        'btn_delete'      => 'মুছুন',
        'protected'       => 'সুরক্ষিত',
        'confirm_delete'  => 'আপনি কি এই ব্যবহারকারীকে মুছে ফেলতে চান?',
        'no_users'        => 'কোনো ব্যবহারকারী পাওয়া যায়নি',
        'no_users_sub'    => 'এই বিভাগে কোনো ব্যবহারকারী নেই।',
        'admin'           => 'অ্যাডমিন',
        'employer'        => 'নিয়োগকর্তা',
        'job_seeker'      => 'চাকরিপ্রার্থী',
        'na'              => 'প্রযোজ্য নয়',
        'showing'         => 'দেখানো হচ্ছে',
        'of'              => 'এর মধ্যে',
        'users_lbl'       => 'জন ব্যবহারকারী',
    ],
    'en' => [
        'page_title'      => 'Manage Users — Jibika Portal',
        'title'           => 'Manage Users',
        'subtitle'        => 'Review and manage all users registered on the platform.',
        'back_btn'        => 'Back to Dashboard',
        'search_ph'       => 'Search by name or email...',
        'total_users'     => 'Total Users',
        'filter_all'      => 'All',
        'filter_seeker'   => 'Job Seekers',
        'filter_employer' => 'Employers',
        'filter_admin'    => 'Admins',
        'th_serial'       => '#',
        'th_name'         => 'Name',
        'th_email'        => 'Email',
        'th_phone'        => 'Phone',
        'th_role'         => 'Role',
        'th_created'      => 'Joined On',
        'th_action'       => 'Action',
        'btn_delete'      => 'Delete',
        'protected'       => 'Protected',
        'confirm_delete'  => 'Delete this user?',
        'no_users'        => 'No Users Found',
        'no_users_sub'    => 'No users in this category.',
        'admin'           => 'Admin',
        'employer'        => 'Employer',
        'job_seeker'      => 'Job Seeker',
        'na'              => 'N/A',
        'showing'         => 'Showing',
        'of'              => 'of',
        'users_lbl'       => 'users',
    ]
];
$ct = $auText[$lang];

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE user_id = '$id' AND role != 'admin'");
    }
    header("Location: users.php");
    exit();
}

$result = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
$users = [];
$counts = ['all' => 0, 'job_seeker' => 0, 'employer' => 0, 'admin' => 0];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
        $counts['all']++;
        if (isset($counts[$row['role']])) $counts[$row['role']]++;
    }
}
?>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<style>
    body { background: #f0f4f8; }

    .admin-users-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 60%, #1a2640 100%);
        color: white;
        padding: 40px 0 30px;
        position: relative;
        overflow: hidden;
    }
    .admin-users-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 240px; height: 240px;
        background: rgba(99,102,241,0.12);
        border-radius: 50%;
    }
    .admin-users-hero::after {
        content: '';
        position: absolute;
        bottom: -80px; left: -40px;
        width: 300px; height: 300px;
        background: rgba(16,185,129,0.07);
        border-radius: 50%;
    }

    .stat-badge {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 12px 24px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        backdrop-filter: blur(4px);
    }

    .users-table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        overflow: hidden;
    }

    .users-toolbar {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 220px;
        position: relative;
    }
    .search-box input {
        width: 100%;
        padding: 10px 16px 10px 42px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.92rem;
        transition: border-color 0.2s;
        outline: none;
        background: white;
    }
    .search-box input:focus { border-color: #6366f1; }
    .search-box .search-icon {
        position: absolute;
        left: 14px; top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .filter-pills { display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-pill {
        padding: 7px 16px;
        border-radius: 20px;
        border: 1.5px solid #e2e8f0;
        background: white;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        color: #64748b;
    }
    .filter-pill:hover { border-color: #6366f1; color: #6366f1; }
    .filter-pill.active { background: #6366f1; border-color: #6366f1; color: white; }
    .filter-pill .pill-count {
        display: inline-block;
        background: rgba(255,255,255,0.25);
        color: inherit;
        border-radius: 10px;
        padding: 1px 7px;
        font-size: 0.75rem;
        margin-left: 4px;
    }
    .filter-pill:not(.active) .pill-count {
        background: #f1f5f9;
        color: #64748b;
    }

    .table thead th {
        background: #f8fafc;
        color: #374151;
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 2px solid #e5e7eb;
        padding: 14px 16px;
        white-space: nowrap;
    }
    .table tbody tr { transition: background 0.15s; }
    .table tbody tr:hover { background: #f8fafc; }
    .table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #374151;
    }

    .user-cell { display: flex; align-items: center; gap: 12px; }
    .user-avatar {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1rem; color: white;
        flex-shrink: 0;
    }
    .user-name { font-weight: 600; color: #1e293b; font-size: 0.92rem; }
    .user-id { font-size: 0.73rem; color: #94a3b8; }

    .role-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .role-admin    { background: #1e293b; color: #f8fafc; }
    .role-employer { background: #d1fae5; color: #065f46; }
    .role-seeker   { background: #dbeafe; color: #1d4ed8; }

    .btn-del-user {
        background: white;
        color: #dc2626;
        border: 1.5px solid #fca5a5;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-del-user:hover {
        background: #dc2626;
        color: white;
        border-color: #dc2626;
        transform: translateY(-1px);
    }
    .protected-text {
        font-size: 0.8rem;
        color: #94a3b8;
        font-weight: 500;
        display: flex; align-items: center; gap: 5px;
    }

    .empty-state {
        padding: 80px 20px;
        text-align: center;
    }
    .empty-state-icon {
        width: 90px; height: 90px;
        background: linear-gradient(135deg, #f5f3ff, #ede9fe);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        font-size: 2.2rem;
        color: #7c3aed;
    }

    .table-footer-bar {
        padding: 14px 24px;
        border-top: 1px solid #f1f5f9;
        background: #fafbfc;
        font-size: 0.85rem;
        color: #64748b;
    }

    .hidden-row { display: none !important; }

    .avatar-colors {
        --c0: #6366f1; --c1: #ec4899; --c2: #f59e0b; --c3: #10b981;
        --c4: #3b82f6; --c5: #8b5cf6; --c6: #06b6d4; --c7: #ef4444;
    }
</style>

<!-- Hero Section -->
<div class="admin-users-hero">
    <div class="container-fluid px-4 px-lg-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1 fs-3">
                    <i class="fa-solid fa-users-gear me-2"></i><?php echo $ct['title']; ?>
                </h2>
                <p class="mb-0 opacity-75"><?php echo $ct['subtitle']; ?></p>
            </div>
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <div class="stat-badge">
                    <i class="fa-solid fa-users fs-5"></i>
                    <div>
                        <div style="font-size:0.75rem; opacity:0.75;"><?php echo $ct['total_users']; ?></div>
                        <div style="font-size:1.4rem; font-weight:800;" id="totalCount"><?php echo translateNumber($counts['all'], $lang); ?></div>
                    </div>
                </div>
                <a href="dashboard.php" class="btn btn-light fw-bold px-4 rounded-pill">
                    <i class="fa-solid fa-arrow-left me-2"></i><?php echo $ct['back_btn']; ?>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 px-lg-5 py-4 avatar-colors">
    <div class="users-table-card">

        <!-- Toolbar -->
        <div class="users-toolbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="userSearch" placeholder="<?php echo htmlspecialchars($ct['search_ph']); ?>" oninput="filterUsers()">
            </div>
            <div class="filter-pills">
                <button class="filter-pill active" onclick="setFilter('all', this)">
                    <?php echo $ct['filter_all']; ?> <span class="pill-count"><?php echo translateNumber($counts['all'], $lang); ?></span>
                </button>
                <button class="filter-pill" onclick="setFilter('job_seeker', this)">
                    <i class="fa-solid fa-user-graduate" style="font-size:0.75rem;"></i>
                    <?php echo $ct['filter_seeker']; ?> <span class="pill-count"><?php echo translateNumber($counts['job_seeker'], $lang); ?></span>
                </button>
                <button class="filter-pill" onclick="setFilter('employer', this)">
                    <i class="fa-solid fa-building" style="font-size:0.75rem;"></i>
                    <?php echo $ct['filter_employer']; ?> <span class="pill-count"><?php echo translateNumber($counts['employer'], $lang); ?></span>
                </button>
                <button class="filter-pill" onclick="setFilter('admin', this)">
                    <i class="fa-solid fa-shield" style="font-size:0.75rem;"></i>
                    <?php echo $ct['filter_admin']; ?> <span class="pill-count"><?php echo translateNumber($counts['admin'], $lang); ?></span>
                </button>
            </div>
            <div id="countDisplay" class="text-muted ms-auto" style="font-size:0.88rem; white-space:nowrap;">
                <?php echo $ct['showing']; ?> <strong id="visibleCount"><?php echo translateNumber($counts['all'], $lang); ?></strong>
                <?php echo $ct['of']; ?> <strong><?php echo translateNumber($counts['all'], $lang); ?></strong> <?php echo $ct['users_lbl']; ?>
            </div>
        </div>

        <!-- Table -->
        <?php if (count($users) > 0): ?>
        <div class="table-responsive">
            <table class="table mb-0" id="usersTable">
                <thead>
                    <tr>
                        <th style="width:50px;"><?php echo $ct['th_serial']; ?></th>
                        <th><?php echo $ct['th_name']; ?></th>
                        <th><?php echo $ct['th_email']; ?></th>
                        <th><?php echo $ct['th_phone']; ?></th>
                        <th><?php echo $ct['th_role']; ?></th>
                        <th><?php echo $ct['th_created']; ?></th>
                        <th style="width:130px;"><?php echo $ct['th_action']; ?></th>
                    </tr>
                </thead>
                <tbody id="usersBody">
                    <?php
                    $avatarColors = ['#6366f1','#ec4899','#f59e0b','#10b981','#3b82f6','#8b5cf6','#06b6d4','#ef4444'];
                    foreach ($users as $i => $row):
                        $color = $avatarColors[$i % count($avatarColors)];
                        $translatedName = translateEmployerName($row['full_name'] ?? '', $lang);
                        $initial = mb_strtoupper(mb_substr($translatedName, 0, 1, 'UTF-8'), 'UTF-8');
                        $roleKey = $row['role'];
                        $roleLabel = $ct[$roleKey] ?? $roleKey;
                        $roleClass = ($roleKey === 'admin') ? 'role-admin' : (($roleKey === 'employer') ? 'role-employer' : 'role-seeker');
                        $roleIcon  = ($roleKey === 'admin') ? 'fa-shield' : (($roleKey === 'employer') ? 'fa-building' : 'fa-user-graduate');
                        $joinedDate = !empty($row['created_at']) ? translateDate(date('d M Y', strtotime($row['created_at'])), $lang) : $ct['na'];
                        $searchData = strtolower(($row['full_name'] ?? '') . ' ' . ($row['email'] ?? '') . ' ' . $roleKey);
                        $serialDisplay = translateNumber($i + 1, $lang);
                        $userIdDisplay = translateNumber($row['user_id'] ?? '', $lang);
                    ?>
                    <tr data-search="<?php echo htmlspecialchars($searchData); ?>"
                        data-role="<?php echo htmlspecialchars($roleKey); ?>">
                        <td class="text-muted fw-bold"><?php echo $serialDisplay; ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar" style="background:<?php echo $color; ?>;">
                                    <?php echo $initial; ?>
                                </div>
                                <div>
                                    <div class="user-name"><?php echo htmlspecialchars($translatedName); ?></div>
                                    <div class="user-id">#<?php echo $userIdDisplay; ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="color:#64748b; font-size:0.88rem;"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td style="color:#64748b; font-size:0.88rem;">
                            <?php echo !empty($row['phone']) ? htmlspecialchars(translateNumber($row['phone'], $lang)) : '<span class="text-muted">—</span>'; ?>
                        </td>
                        <td>
                            <span class="role-badge <?php echo $roleClass; ?>">
                                <i class="fa-solid <?php echo $roleIcon; ?>" style="font-size:0.73rem;"></i>
                                <?php echo $roleLabel; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px; font-size:0.85rem; color:#64748b;">
                                <i class="fa-regular fa-calendar" style="font-size:0.78rem;"></i>
                                <?php echo $joinedDate; ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($row['role'] !== 'admin'): ?>
                                <a href="users.php?delete=<?php echo $row['user_id']; ?>"
                                   class="btn-del-user"
                                   onclick="return confirm('<?php echo addslashes($ct['confirm_delete']); ?>')">
                                    <i class="fa-solid fa-trash me-1"></i><?php echo $ct['btn_delete']; ?>
                                </a>
                            <?php else: ?>
                                <span class="protected-text">
                                    <i class="fa-solid fa-lock"></i><?php echo $ct['protected']; ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="table-footer-bar d-flex justify-content-between align-items-center">
            <span><?php echo $ct['showing']; ?> <span id="footerVisible"><?php echo translateNumber($counts['all'], $lang); ?></span> <?php echo $ct['of']; ?> <?php echo translateNumber($counts['all'], $lang); ?> <?php echo $ct['users_lbl']; ?></span>
            <span class="text-primary fw-bold"><i class="fa-solid fa-circle-check me-1"></i><?php echo $ct['title']; ?></span>
        </div>

        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fa-solid fa-users-slash"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2"><?php echo $ct['no_users']; ?></h5>
            <p class="text-muted mb-0"><?php echo $ct['no_users_sub']; ?></p>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
var currentFilter = 'all';

function setFilter(role, btn) {
    currentFilter = role;
    document.querySelectorAll('.filter-pill').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    filterUsers();
}

function filterUsers() {
    const query = document.getElementById('userSearch').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#usersBody tr');
    let visible = 0;
    rows.forEach(function(row) {
        const data   = row.getAttribute('data-search') || '';
        const role   = row.getAttribute('data-role') || '';
        const matchQ = !query || data.includes(query);
        const matchR = currentFilter === 'all' || role === currentFilter;
        if (matchQ && matchR) {
            row.classList.remove('hidden-row');
            visible++;
        } else {
            row.classList.add('hidden-row');
        }
    });
    const vc = document.getElementById('visibleCount');
    const fv = document.getElementById('footerVisible');
    if (vc) vc.textContent = visible;
    if (fv) fv.textContent = visible;
}
</script>

<?php include('../includes/footer.php'); ?>