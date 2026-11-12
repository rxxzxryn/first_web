<?php
class Student
{
    public string $stdid;
    public string $stdname;
    public int $age;

    public function __construct(string $stdid, string $stdname, int $age)
    {
        $this->stdid = $stdid;
        $this->stdname = $stdname;
        $this->age = $age;
    }

    public function getInfo(): string
    {
        return "รหัสนักศึกษา: {$this->stdid} ชื่อ: {$this->stdname} อายุ: {$this->age}";
    }
}

$isSubmitted = $_SERVER['REQUEST_METHOD'] === 'POST';
$studentInfo = null;
$formData = [
    'stdid' => '',
    'stdname' => '',
    'age' => '',
    'phone' => '',
    'department' => '',
    'gpa' => '',
    'advisor' => '',
    'room' => '',
    'address' => '',
];
$isValid = false;

if ($isSubmitted) {
    foreach ($formData as $key => $value) {
        $formData[$key] = trim($_POST[$key] ?? '');
    }

    $ageIsValid = filter_var($formData['age'], FILTER_VALIDATE_INT) !== false
        && (int) $formData['age'] >= 0
        && (int) $formData['age'] <= 120;
    $gpaIsValid = is_numeric($formData['gpa'])
        && (float) $formData['gpa'] >= 0
        && (float) $formData['gpa'] <= 4;
    $phoneIsValid = preg_match('/^[0-9]{1,10}$/', $formData['phone']) === 1;
    $requiredFieldsAreFilled = !in_array('', $formData, true);

    $isValid = $requiredFieldsAreFilled && $ageIsValid && $gpaIsValid && $phoneIsValid;
    if ($isValid) {
        $student = new Student($formData['stdid'], $formData['stdname'], (int) $formData['age']);
        $studentInfo = $student->getInfo();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลนักศึกษา</title>
    <style>
        :root {
            color-scheme: light;
            --deep-teal: #1E3E42;
            --teal: #315d60;
            --mint: #dcebe5;
            --paper: #ffffff;
            --ink: #203437;
            --muted: #718184;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 48px 24px;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 12% 18%, rgba(168, 204, 190, 0.6), transparent 28%),
                radial-gradient(circle at 88% 82%, rgba(49, 93, 96, 0.22), transparent 32%),
                linear-gradient(135deg, #edf4f0, #dce8e2 52%, #f4f7f4);
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        .page {
            width: min(100%, 1000px);
        }

        .layout {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            overflow: hidden;
            border: 1px solid rgba(30, 62, 66, 0.12);
            border-radius: 26px;
            background: var(--paper);
            box-shadow: 0 30px 80px rgba(30, 62, 66, 0.2);
        }

        .info-panel {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            min-height: 540px;
            padding: 48px 40px;
            background: var(--deep-teal);
            color: #fff;
        }

        .info-panel::before,
        .info-panel::after {
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(220, 235, 229, 0.15);
            border-radius: 50%;
            content: "";
        }

        .info-panel::before {
            width: 330px;
            height: 330px;
            right: -170px;
            top: -110px;
            box-shadow: 0 0 0 30px rgba(220, 235, 229, 0.035), 0 0 0 65px rgba(220, 235, 229, 0.025);
        }

        .info-panel::after {
            width: 220px;
            height: 220px;
            left: -145px;
            bottom: -125px;
            background: rgba(220, 235, 229, 0.06);
        }

        .eyebrow {
            margin: 0 0 14px;
            color: #b9d1c9;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        h1 {
            max-width: 380px;
            margin: 0;
            font-size: clamp(2rem, 4vw, 2.8rem);
            line-height: 1.2;
        }

        .intro {
            max-width: 360px;
            margin: 18px 0 0;
            color: #d2e0db;
            line-height: 1.8;
        }

        .info-divider {
            width: 48px;
            height: 3px;
            margin: 34px 0 25px;
            border: 0;
            border-radius: 2px;
            background: #9dbdb2;
        }

        .info-title {
            margin: 0 0 16px;
            color: #c8dcd4;
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .info-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin: 0;
        }

        .info-item {
            min-width: 0;
            padding: 13px 14px;
            border: 1px solid rgba(220, 235, 229, 0.17);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.07);
            overflow-wrap: anywhere;
        }

        .info-item.address-item {
            grid-column: 1 / -1;
        }

        .info-item dt {
            margin-bottom: 5px;
            color: #b9d1c9;
            font-size: 0.78rem;
        }

        .info-item dd {
            margin: 0;
            font-weight: 600;
        }

        .form-panel {
            padding: 48px clamp(26px, 5vw, 58px);
        }

        .form-heading {
            margin: 0;
            color: var(--deep-teal);
            font-size: 1.5rem;
        }

        .form-subtitle {
            margin: 8px 0 24px;
            color: var(--muted);
            font-size: 0.93rem;
        }

        form {
            margin: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 16px;
        }

        .field {
            display: block;
            min-width: 0;
            margin-bottom: 17px;
            color: var(--deep-teal);
            font-size: 0.92rem;
            font-weight: 650;
        }

        .field.full-width {
            grid-column: 1 / -1;
        }

        .field input,
        .field textarea {
            width: 100%;
            margin-top: 8px;
            padding: 0 13px;
            border: 1px solid #d5e0dc;
            border-radius: 11px;
            outline: none;
            background: #fbfdfc;
            color: var(--ink);
            font: inherit;
            font-weight: 400;
            transition: border-color 160ms ease, box-shadow 160ms ease;
        }

        .field input {
            height: 48px;
        }

        .field textarea {
            min-height: 100px;
            padding-top: 12px;
            resize: vertical;
        }

        .field input:focus,
        .field textarea:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(49, 93, 96, 0.13);
        }

        .field input::placeholder,
        .field textarea::placeholder {
            color: #9aa8a6;
        }

        .field-hint {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 0.76rem;
            font-weight: 400;
        }

        .field input[type="number"] {
            appearance: textfield;
            -moz-appearance: textfield;
        }

        .field input[type="number"]::-webkit-inner-spin-button,
        .field input[type="number"]::-webkit-outer-spin-button {
            margin: 0;
            -webkit-appearance: none;
        }

        .submit-button {
            width: 100%;
            min-height: 52px;
            margin-top: 5px;
            border: 0;
            border-radius: 11px;
            background: var(--deep-teal);
            color: #fff;
            cursor: pointer;
            font: inherit;
            font-size: 1rem;
            font-weight: 700;
            transition: background 160ms ease, transform 160ms ease;
        }

        .submit-button:hover {
            transform: translateY(-1px);
            background: var(--teal);
        }

        .submit-button:focus-visible {
            outline: 3px solid #9dbdb2;
            outline-offset: 3px;
        }

        .validation-message {
            margin: 16px 0 0;
            padding: 13px 15px;
            border: 1px solid #efd6c9;
            border-radius: 10px;
            background: #fff6f0;
            color: #75412e;
            line-height: 1.6;
        }

        @media (max-width: 720px) {
            body {
                padding: 24px 14px;
            }

            .layout {
                grid-template-columns: 1fr;
                border-radius: 20px;
            }

            .info-panel {
                min-height: auto;
                padding: 34px 28px;
            }

            .info-divider {
                margin: 24px 0 18px;
            }

            .info-list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .form-panel {
                padding: 34px 28px;
            }
        }

        @media (max-width: 420px) {
            .info-list,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .field.full-width,
            .info-item.address-item {
                grid-column: auto;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="layout">
            <aside class="info-panel">
                <p class="eyebrow">Student information</p>
                <h1>ข้อมูลนักศึกษา</h1>
                <?php if ($isValid): ?>
                    <p class="intro">แสดงข้อมูลที่กรอกไว้ทางด้านซ้าย</p>
                <?php else: ?>
                    <p class="intro">กรอกแบบฟอร์มทางด้านขวา แล้วตรวจดูข้อมูลนักศึกษาได้ที่แผงนี้</p>
                <?php endif; ?>
                <hr class="info-divider">
                <p class="info-title"><?= $isValid ? 'ข้อมูลนักศึกษา' : 'รายละเอียดที่จะเพิ่ม' ?></p>
                <dl class="info-list">
                    <?php if ($isValid): ?>
                        <div class="info-item">
                            <dt>รหัสนักศึกษา</dt>
                            <dd><?= htmlspecialchars($formData['stdid'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="info-item">
                            <dt>ชื่อนักศึกษา</dt>
                            <dd><?= htmlspecialchars($formData['stdname'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="info-item">
                            <dt>อายุ</dt>
                            <dd><?= htmlspecialchars($formData['age'], ENT_QUOTES, 'UTF-8') ?> ปี</dd>
                        </div>
                        <div class="info-item">
                            <dt>เบอร์โทรศัพท์</dt>
                            <dd><?= htmlspecialchars($formData['phone'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="info-item">
                            <dt>แผนกวิชา</dt>
                            <dd><?= htmlspecialchars($formData['department'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="info-item">
                            <dt>เกรดเฉลี่ย</dt>
                            <dd><?= htmlspecialchars($formData['gpa'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="info-item">
                            <dt>ครูที่ปรึกษา</dt>
                            <dd><?= htmlspecialchars($formData['advisor'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="info-item">
                            <dt>ห้อง</dt>
                            <dd><?= htmlspecialchars($formData['room'], ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="info-item address-item">
                            <dt>ที่อยู่</dt>
                            <dd><?= nl2br(htmlspecialchars($formData['address'], ENT_QUOTES, 'UTF-8')) ?></dd>
                        </div>
                    <?php else: ?>
                        <div class="info-item">
                            <dt>รหัสนักศึกษา</dt>
                            <dd>จะแสดงหลังกรอกข้อมูล</dd>
                        </div>
                        <div class="info-item">
                            <dt>ชื่อนักศึกษา</dt>
                            <dd>จะแสดงหลังกรอกข้อมูล</dd>
                        </div>
                        <div class="info-item">
                            <dt>อายุ</dt>
                            <dd>จะแสดงหลังกรอกข้อมูล</dd>
                        </div>
                        <div class="info-item">
                            <dt>เบอร์โทรศัพท์</dt>
                            <dd>จะแสดงหลังกรอกข้อมูล</dd>
                        </div>
                        <div class="info-item">
                            <dt>แผนกวิชา</dt>
                            <dd>จะแสดงหลังกรอกข้อมูล</dd>
                        </div>
                        <div class="info-item">
                            <dt>เกรดเฉลี่ย</dt>
                            <dd>จะแสดงหลังกรอกข้อมูล</dd>
                        </div>
                        <div class="info-item">
                            <dt>ครูที่ปรึกษา</dt>
                            <dd>จะแสดงหลังกรอกข้อมูล</dd>
                        </div>
                        <div class="info-item">
                            <dt>ห้อง</dt>
                            <dd>จะแสดงหลังกรอกข้อมูล</dd>
                        </div>
                        <div class="info-item address-item">
                            <dt>ที่อยู่</dt>
                            <dd>จะแสดงหลังกรอกข้อมูล</dd>
                        </div>
                    <?php endif; ?>
                </dl>
            </aside>

            <div class="form-panel">
                <h2 class="form-heading">เพิ่มข้อมูลนักศึกษา</h2>
                <p class="form-subtitle">กรอกข้อมูลให้ครบทุกช่อง</p>
                <form method="post" action="">
                    <div class="form-grid">
                        <label class="field" for="stdid">
                            รหัสนักศึกษา
                            <input id="stdid" type="text" name="stdid" value="<?= htmlspecialchars($formData['stdid'], ENT_QUOTES, 'UTF-8') ?>" placeholder="เช่น 69319010001" required>
                        </label>

                        <label class="field" for="stdname">
                            ชื่อ-นามสกุล
                            <input id="stdname" type="text" name="stdname" value="<?= htmlspecialchars($formData['stdname'], ENT_QUOTES, 'UTF-8') ?>" placeholder="กรอกชื่อ-นามสกุล" required>
                        </label>

                        <label class="field" for="age">
                            อายุ
                            <input id="age" type="number" name="age" min="0" max="120" step="1" value="<?= htmlspecialchars($formData['age'], ENT_QUOTES, 'UTF-8') ?>" placeholder="0–120 ปี" required>
                        </label>

                        <label class="field" for="phone">
                            เบอร์โทรศัพท์
                            <input id="phone" type="tel" name="phone" inputmode="numeric" maxlength="10" value="<?= htmlspecialchars($formData['phone'], ENT_QUOTES, 'UTF-8') ?>" placeholder="ตัวเลขไม่เกิน 10 หลัก" pattern="[0-9]{1,10}" title="กรอกตัวเลข 1–10 หลักเท่านั้น" required>
                        </label>

                        <label class="field" for="department">
                            แผนกวิชา
                            <input id="department" type="text" name="department" value="<?= htmlspecialchars($formData['department'], ENT_QUOTES, 'UTF-8') ?>" placeholder="เช่น เทคโนโลยีสารสนเทศ" required>
                        </label>

                        <label class="field" for="gpa">
                            เกรดเฉลี่ย (GPA)
                            <input id="gpa" type="number" name="gpa" min="0" max="4" step="0.01" value="<?= htmlspecialchars($formData['gpa'], ENT_QUOTES, 'UTF-8') ?>" placeholder="0.00 - 4.00" required>
                        </label>

                        <label class="field" for="advisor">
                            ครูที่ปรึกษา
                            <input id="advisor" type="text" name="advisor" value="<?= htmlspecialchars($formData['advisor'], ENT_QUOTES, 'UTF-8') ?>" placeholder="ชื่อครูที่ปรึกษา" required>
                        </label>

                        <label class="field" for="room">
                            ห้อง
                            <input id="room" type="text" name="room" value="<?= htmlspecialchars($formData['room'], ENT_QUOTES, 'UTF-8') ?>" placeholder="เช่น 3/1" required>
                        </label>

                        <label class="field full-width" for="address">
                            ที่อยู่
                            <textarea id="address" name="address" placeholder="บ้านเลขที่ ถนน ตำบล/แขวง อำเภอ/เขต จังหวัด และรหัสไปรษณีย์" required><?= htmlspecialchars($formData['address'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </label>
                    </div>
                    <input class="submit-button" type="submit" value="เพิ่มข้อมูลนักศึกษา">
                </form>
                <?php if ($isSubmitted && !$isValid): ?>
                    <div class="validation-message" role="alert">
                        กรุณากรอกข้อมูลให้ครบถ้วน อายุ 0–120 ปี เบอร์โทรศัพท์เป็นตัวเลข 1–10 หลัก และเกรดเฉลี่ย 0.00–4.00
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
