
<?php 
session_start();

$errors  = [];

$registered = false;

$name = "";
$email = "";
$mobile = "";
$governorate = "";
$track = "";
$skills = [];
$message = "";


$allowedGovernorates = [
  "Amman",
  "Irbid",
  "Aqaba",
  "Zarqa"
];

$allowedTracks = [
  "Full Stack",
  "Frontend",
  "Backend"
];

$allowedSkills = [
  "HTML",
  "CSS",
  "JavaScript"
];

function clean($value) {
  return trim($value);
}


function escape($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}


class Registration {
  public $name;
  public $email;
  public $mobile;
  public $governorate;
  public $track;
  public $skills;
  public $message;

  public function __construct(
    $name,
    $email,
    $mobile,
    $governorate,
    $track,
    $skills,
    $message
  ) {
    $this->name = $name;
    $this->email = $email;
    $this->mobile = $mobile;
    $this->governorate = $governorate;
    $this->track = $track;
    $this->skills = $skills;
    $this->message = $message;
  }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = clean($_POST["name"] ?? "");
    $email = clean($_POST["email"] ?? "");
    $mobile = clean($_POST["mobile"] ?? "");
    $governorate = clean($_POST["governorate"] ?? "");
    $track = clean($_POST["track"] ?? "");
    $message = clean($_POST["message"] ?? "");

    $submittedSkills = $_POST["skills"] ?? [];

    if (is_array($submittedSkills)) {
        foreach ($submittedSkills as $skill) {
            if (is_string($skill)) {
                $skills[] = clean($skill);
            }
        }
    }

    if ($name === "") {
        $errors["name"] = "Full name is required";
    }

    if ($email === "") {
        $errors["email"] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Enter a valid email address";
    }

    if ($mobile === "") {
        $errors["mobile"] = "Mobile is required";
    } elseif (!preg_match('/^(?:\+962|00962|0)?7[789][0-9]{7}$/', $mobile)) {
        $errors["mobile"] = "Enter a valid Jordanian mobile number";
    }

    if (!in_array($governorate, $allowedGovernorates, true)) {
        $errors["governorate"] = "Please select a valid governorate";
    }

    if (!in_array($track, $allowedTracks, true)) {
        $errors["track"] = "Please select a valid track";
    }

    if (
        count($skills) === 0 ||
        count(array_diff($skills, $allowedSkills)) > 0
    ) {
        $errors["skills"] = "Select at least one valid skill";
    }

    if (($_POST["agree"] ?? "") !== "yes") {
        $errors["agree"] = "You must accept the terms";
    }

    if (empty($errors)) {

        $registration = new Registration(
            $name,
            $email,
            $mobile,
            $governorate,
            $track,
            $skills,
            $message
        );

        $_SESSION["registration"] = [
            "name" => $registration->name,
            "email" => $registration->email,
            "mobile" => $registration->mobile,
            "governorate" => $registration->governorate,
            "track" => $registration->track,
            "skills" => $registration->skills,
            "message" => $registration->message
        ];

        setcookie("saved_name", $registration->name, [
            "expires" => time() + (30 * 24 * 60 * 60),
            "path" => "/",
            "secure" => !empty($_SERVER["HTTPS"]) &&
                        $_SERVER["HTTPS"] !== "off",
            "httponly" => true,
            "samesite" => "Lax"
        ]);

        $registered = true;
    }
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form</title>
    <link rel="stylesheet" href="style(1).css">
</head>
<body>

<?php if ($registered): ?>

  <div class="success">
        <h2>Registration Successful!</h2>
        <p>Your information has been saved in the session.</p>
        <a href="profile.php">View your registration</a>
    </div>

    <section>
        <h2>Registration Details</h2>
        <p>Name: <?= escape($registration->name) ?></p>
        <p>Email: <?= escape($registration->email) ?></p>
        <p>Mobile: <?= escape($registration->mobile) ?></p>
        <p>Governorate: <?= escape($registration->governorate) ?></p>
        <p>Track: <?= escape($registration->track) ?></p>
        <p>Skills: <?= escape(implode(", ", $registration->skills)) ?></p>
        <p>Message: <?= escape($registration->message) ?></p>
    </section>

    <?php else: ?>
   <form action="" method="post">

  <label for="name">Full Name</label>
        <input type="text" id="name" name="name"
               value="<?= escape($name) ?>">
        <?php if (isset($errors["name"])): ?>
            <p class="error"><?= escape($errors["name"]) ?></p>
        <?php endif; ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               value="<?= escape($email) ?>">
        <?php if (isset($errors["email"])): ?>
            <p class="error"><?= escape($errors["email"]) ?></p>
        <?php endif; ?>

        <label for="mobile">Mobile</label>
        <input type="tel" id="mobile" name="mobile"
               placeholder="0791234567"
               value="<?= escape($mobile) ?>">
        <?php if (isset($errors["mobile"])): ?>
            <p class="error"><?= escape($errors["mobile"]) ?></p>
        <?php endif; ?>

        <label for="governorate">Governorate</label>
        <select id="governorate" name="governorate">
            <option value="">Choose a governorate</option>
            <?php foreach ($allowedGovernorates as $city): ?>
                <option value="<?= escape($city) ?>"
                    <?= $governorate === $city ? "selected" : "" ?>>
                    <?= escape($city) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors["governorate"])): ?>
            <p class="error"><?= escape($errors["governorate"]) ?></p>
        <?php endif; ?>

        <label>Track</label>
        <?php foreach ($allowedTracks as $option): ?>
            <label class="inline">
                <input type="radio" name="track"
                       value="<?= escape($option) ?>"
                       <?= $track === $option ? "checked" : "" ?>>
                <?= escape($option) ?>
            </label>
        <?php endforeach; ?>
        <?php if (isset($errors["track"])): ?>
            <p class="error"><?= escape($errors["track"]) ?></p>
        <?php endif; ?>

  <label>Skills you already have</label>
        <?php foreach ($allowedSkills as $skill): ?>
            <label class="inline">
                <input type="checkbox" name="skills[]"
                       value="<?= escape($skill) ?>"
                       <?= in_array($skill, $skills, true) ? "checked" : "" ?>>
                <?= escape($skill) ?>
            </label>
        <?php endforeach; ?>
        <?php if (isset($errors["skills"])): ?>
            <p class="error"><?= escape($errors["skills"]) ?></p>
        <?php endif; ?>

        <label for="message">Why do you want to join? (optional)</label>
        <textarea id="message" name="message" rows="4"><?= escape($message) ?></textarea>

        <label class="inline terms">
            <input type="checkbox" name="agree" value="yes"
                   <?= isset($_POST["agree"]) && $_POST["agree"] === "yes" ? "checked" : "" ?>>
            I agree to the academy terms
        </label>
        <?php if (isset($errors["agree"])): ?>
            <p class="error"><?= escape($errors["agree"]) ?></p>
        <?php endif; ?>

        <button type="submit">Register</button>

</form> 
<?php endif; ?>
</body>
</html>