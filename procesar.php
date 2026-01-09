<?php
// procesar.php - V5: Folios Rojos, Fix Acentos y Renombrado
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// --- HELPER 1: Decodificación para FPDF ---
function safeDecode($str) {
    if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
    }
    return utf8_decode($str);
}

// --- HELPER 2: Mayúsculas Acentuadas (Fuerza Bruta) ---
// Soluciona el problema de "canción" -> "CANCIóN"
function customToUpper($str) {
    $str = mb_strtoupper($str, 'UTF-8');
    $reemplazos = [
        'á' => 'Á', 'é' => 'É', 'í' => 'Í', 'ó' => 'Ó', 'ú' => 'Ú',
        'ñ' => 'Ñ', 'ü' => 'Ü'
    ];
    $str = strtr($str, $reemplazos);
    return safeDecode($str);
}

// Carga Librerías
$libDir = __DIR__ . '/libs/';
function buscarArchivo($d, $n) {
    if(!is_dir($d)) return null; $s=scandir($d);
    foreach($s as $f){ if($f=='.'||$f=='..')continue; $p=$d.'/'.$f;
    if(is_file($p)&&$f==$n)return $d.'/'; if(is_dir($p))if($r=buscarArchivo($p,$n))return $r; } return null;
}
$fpdfPath = 'libs/fpdf/fpdf.php'; 
if (!file_exists($fpdfPath)) $fpdfPath = buscarArchivo(__DIR__, 'fpdf.php') . 'fpdf.php';
require_once $fpdfPath;

$mailerPath = buscarArchivo($libDir, 'PHPMailer.php');
if ($mailerPath) { require_once $mailerPath.'Exception.php'; require_once $mailerPath.'PHPMailer.php'; require_once $mailerPath.'SMTP.php'; }

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] != 'POST') die("Acceso denegado.");

// 1. Recibir Datos
$supervisor = $_POST['supervisor'];
$fecha_suceso = $_POST['fecha_suceso'];
$area = $_POST['area'];
$ubicacion = $_POST['ubicacion'];
$titulo_suceso = $_POST['titulo_suceso'];
$involucrados = $_POST['involucrados'];
$descripcion = $_POST['descripcion'];
$ia_used = $_POST['descripcion_ia'];

// 2. Procesar Imágenes
$anio = date('Y'); $mes = date('m');
$targetDir = "uploads/$anio/$mes/";
if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);

function procesarImagen($fileInputName, $targetDir) {
    if(isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
        $tmpName = $_FILES[$fileInputName]['tmp_name'];
        list($w, $h, $type) = getimagesize($tmpName);
        switch ($type) {
            case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($tmpName); break;
            case IMAGETYPE_PNG: $src = imagecreatefrompng($tmpName); break;
            default: return null;
        }
        $maxW = 600; 
        if($w > $maxW) { $ratio = $maxW/$w; $newW = $maxW; $newH = $h*$ratio; } else { $newW=$w; $newH=$h; }
        $dst = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($dst, $src, 0,0,0,0, $newW, $newH, $w, $h);
        $filename = uniqid().'.jpg';
        $fullPath = $targetDir.$filename;
        imagejpeg($dst, $fullPath, 70);
        imagedestroy($src); imagedestroy($dst);
        return $fullPath;
    }
    return null;
}

$imgs = [];
if($i=procesarImagen('img1',$targetDir)) $imgs[]=$i;
if($i=procesarImagen('img2',$targetDir)) $imgs[]=$i;
if($i=procesarImagen('img3',$targetDir)) $imgs[]=$i;

// 3. OBTENER FOLIO (Insertar primero)
try {
    $sql = "INSERT INTO reportes (supervisor, fecha_suceso, area, ubicacion, titulo_suceso, involucrados, descripcion, descripcion_ia, img1_path, img2_path, img3_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$supervisor, $fecha_suceso, $area, $ubicacion, $titulo_suceso, $involucrados, $descripcion, $ia_used, $imgs[0]??null, $imgs[1]??null, $imgs[2]??null]);
    
    $folioId = $pdo->lastInsertId();
    $folioTexto = str_pad($folioId, 4, "0", STR_PAD_LEFT); 
} catch (PDOException $e) {
    die("Error crítico BD: " . $e->getMessage());
}

// 4. Generación de PDF
class ReportePDF extends FPDF {
    public $folioDisplay;

    function Header() {
        $this->SetFillColor(255); $this->Rect(0, 0, 216, 45, 'F');
        
        try {
            $this->Image('http://imgfz.com/i/ioW1kPu.png', 10, 8, 22, 0, 'PNG');
            $this->Image('http://imgfz.com/i/z5weTEV.png', 180, 10, 22, 0, 'PNG');
        } catch(Exception $e){}

        $this->SetY(12);
        $this->SetTextColor(30, 58, 138); 
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 8, 'REPORTE DE NOVEDADES', 0, 1, 'C');
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(80);
        $this->Cell(0, 5, safeDecode('SEGURIDAD PATRIMONIAL - SESCA SEGURIDAD PRIVADA'), 0, 1, 'C');
        
        // --- FOLIO ROJO ---
        $this->Ln(4);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(200, 0, 0); // Rojo puro
        // Alineación derecha precisa
        $this->Cell(0, 6, 'Folio-' . $this->folioDisplay . '  ', 0, 1, 'R');

        $this->SetDrawColor(30, 58, 138); $this->SetLineWidth(0.6);
        $this->Line(10, 42, 206, 42);
        $this->Ln(10);
    }

    function SectionTitle($label) {
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(255);
        $this->SetFillColor(30, 58, 138);
        $this->Cell(0, 7, '  ' . customToUpper($label), 0, 1, 'L', true);
        $this->Ln(3);
    }

    function DataRow($label, $value) {
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(100);
        $this->Cell(40, 6, customToUpper($label), 0, 0, 'L');
        
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(0);
        $this->SetFillColor(248); 
        $this->Cell(0, 6, safeDecode($value), 'B', 1, 'L', true);
        $this->Ln(1);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150);
        // Texto Footer Actualizado
        $this->Cell(0, 10, safeDecode('Potenciado por Goratrack Sistemas | Pág ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new ReportePDF();
$pdf->folioDisplay = $folioTexto;
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);
$pdf->SetMargins(15, 15, 15);

// I. DATOS
$pdf->SectionTitle('I. Datos del Suceso');
$pdf->SetDrawColor(200);

$pdf->DataRow('Supervisor', $supervisor);
$pdf->DataRow('Fecha y Hora', date('d/m/Y H:i', strtotime($fecha_suceso)));
$pdf->DataRow('Área / Zona', $area);
$pdf->DataRow('Ubicación Exacta', $ubicacion); 
$pdf->DataRow('¿Qué Sucedió?', $titulo_suceso);
$pdf->DataRow('¿Quiénes?', $involucrados);
$pdf->Ln(4);

// II. DESCRIPCIÓN
$pdf->SectionTitle('II. Descripción Detallada');
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0);
$pdf->MultiCell(0, 5, safeDecode($descripcion));
if($ia_used) {
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'I', 7); $pdf->SetTextColor(30, 58, 138);
    $pdf->Cell(0, 4, safeDecode('✓ Redacción optimizada por IA'), 0, 1, 'R');
}
$pdf->Ln(5);

// III. EVIDENCIA
if (!empty($imgs)) {
    $y = $pdf->GetY();
    if ($y + 60 > 260) $pdf->AddPage();

    $pdf->SectionTitle('III. Evidencia Fotográfica');
    
    $margin = 15;
    $pageWidth = 216 - ($margin * 2);
    $gap = 3; 
    $photoW = ($pageWidth - ($gap * 2)) / 3; 
    $photoH = 45; 

    $currentX = $margin;
    $currentY = $pdf->GetY();

    $pdf->SetDrawColor(200);
    $pdf->Rect($currentX, $currentY, $photoW, $photoH);
    $pdf->Rect($currentX + $photoW + $gap, $currentY, $photoW, $photoH);
    $pdf->Rect($currentX + ($photoW + $gap)*2, $currentY, $photoW, $photoH);

    foreach($imgs as $idx => $imgpath) {
        $xPos = $currentX + ($idx * ($photoW + $gap));
        $pdf->Image($imgpath, $xPos, $currentY, $photoW, $photoH);
    }
}

// Generar nombre de archivo con Folio
$pdfFileName = "Reporte_Novedad_Folio_{$folioTexto}.pdf";
$pdfPath = $targetDir.$pdfFileName;
$pdf->Output('F', $pdfPath);

// Actualizar BD con ruta PDF final
$stmtUpd = $pdo->prepare("UPDATE reportes SET pdf_path = ? WHERE id = ?");
$stmtUpd->execute([$pdfPath, $folioId]);

// Enviar Correo
if ($mailerPath) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP(); $mail->Host=SMTP_HOST; $mail->SMTPAuth=true; $mail->Username=SMTP_USER; $mail->Password=SMTP_PASS; $mail->SMTPSecure=SMTP_SECURE; $mail->Port=SMTP_PORT; $mail->CharSet='UTF-8';
        $mail->setFrom(SMTP_USER, 'C4 SESCA Reports');
        $mail->addAddress('william@goratrack.mx');
        $mail->isHTML(true);
        
        $mail->Subject = "Novedad #$folioTexto: $titulo_suceso";
        
        $mail->Body = "<h3>Reporte de Novedad - Folio <span style='color:red'>$folioTexto</span></h3>
        <ul>
            <li><b>Supervisor:</b> $supervisor</li>
            <li><b>Suceso:</b> $titulo_suceso</li>
            <li><b>Ubicación:</b> $ubicacion ($area)</li>
        </ul>
        <p>Potenciado por Goratrack Sistemas.</p>";
        
        $mail->addAttachment($pdfPath, $pdfFileName);
        
        $mail->send();
    } catch (Exception $e) {}
}

echo "<script>alert('✅ Reporte Folio #$folioTexto registrado correctamente.'); window.location.href='index.php';</script>";
?>