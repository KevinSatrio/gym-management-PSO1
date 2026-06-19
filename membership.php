<?php
include 'includes/dbh.inc.php';

/* =========================
   DELETE
========================= */
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM membership_plans WHERE id=$id");
    header("Location: membership.php");
}

/* =========================
   INSERT / CREATE
========================= */
if(isset($_POST['save'])) {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $duration = $_POST['duration_months'];
    $desc = $_POST['description'];

    mysqli_query($conn, "INSERT INTO membership_plans 
    (name, price, duration_months, description)
    VALUES ('$name', '$price', '$duration', '$desc')");

    header("Location: membership.php");
}

/* =========================
   UPDATE (ambil data edit)
========================= */
$editData = null;

if(isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM membership_plans WHERE id=$id");
    $editData = mysqli_fetch_assoc($result);
}

/* UPDATE PROCESS */
if(isset($_POST['update'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $duration = $_POST['duration_months'];
    $desc = $_POST['description'];

    mysqli_query($conn, "UPDATE membership_plans SET
        name='$name',
        price='$price',
        duration_months='$duration',
        description='$desc'
        WHERE id=$id
    ");

    header("Location: membership.php");
}

/* =========================
   READ DATA
========================= */
$result = mysqli_query($conn, "SELECT * FROM membership_plans");
?>

<h2>MEMBERSHIP CRUD (SINGLE FILE)</h2>

<!-- FORM CREATE / UPDATE -->
<form method="POST">
    <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">

    Nama:
    <input type="text" name="name" value="<?= $editData['name'] ?? '' ?>"><br><br>

    Harga:
    <input type="number" name="price" value="<?= $editData['price'] ?? '' ?>"><br><br>

    Durasi:
    <input type="number" name="duration_months" value="<?= $editData['duration_months'] ?? '' ?>"><br><br>

    Deskripsi:
    <textarea name="description"><?= $editData['description'] ?? '' ?></textarea><br><br>

    <?php if($editData): ?>
        <button type="submit" name="update">Update</button>
    <?php else: ?>
        <button type="submit" name="save">Simpan</button>
    <?php endif; ?>
</form>

<hr>

<!-- TABLE READ -->
<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Nama</th>
    <th>Harga</th>
    <th>Durasi</th>
    <th>Aksi</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['price'] ?></td>
    <td><?= $row['duration_months'] ?> bulan</td>
    <td>
        <a href="membership.php?edit=<?= $row['id'] ?>">Edit</a> |
        <a href="membership.php?delete=<?= $row['id'] ?>" 
           onclick="return confirm('Hapus data?')">
           Delete
        </a>
    </td>
</tr>
<?php } ?>
</table>