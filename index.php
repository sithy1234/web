<?php
require __DIR__ . '/db.php';

// Everything below is only sent to people who are logged in.
$user = require_login();

$author = 'ដៃ ចន';   // name shown on the title slide

// ---------- Small builders for the slide content ----------
function box(string $title, string $body, string $kind = 'gold', string $icon = ''): string
{
    $ic = $icon !== '' ? '<span class="ico" aria-hidden="true">' . $icon . '</span>' : '';
    return '<div class="box box-' . $kind . '"><h3>' . $ic . $title . '</h3><div class="box-body">' . $body . '</div></div>';
}

function items(array $li, string $type = 'alpha'): string
{
    return '<ol class="' . $type . '"><li>' . implode('</li><li>', $li) . '</li></ol>';
}

function dots(?int $stage): string
{
    $kh = ['១', '២', '៣', '៤', '៥'];
    $out = '<div class="dots" aria-label="ដំណាក់កាល">';
    foreach ($kh as $i => $d) {
        $n = $i + 1;
        $class = $stage === $n ? 'active' : ($stage !== null && $n < $stage ? 'done' : '');
        $out .= '<span class="' . $class . '">' . $d . '</span>';
    }
    return $out . '</div>';
}

function slide(?int $stage, ?string $badge, string $title, string $html): array
{
    return ['stage' => $stage, 'badge' => $badge, 'title' => $title, 'html' => $html];
}

// Worked solutions (nowdoc keeps the LaTeX backslashes exactly as written).
$solA = <<<'T'
\[\begin{aligned}2^{3x+5}=128 &amp;\iff 2^{3x+5}=2^{7}\\ &amp;\iff 3x+5=7\\ &amp;\iff 3x=2 \Rightarrow x=\frac{2}{3}\end{aligned}\]
T;
$solB = <<<'T'
\[\begin{aligned}5^{x-3}=\frac{1}{25}=\frac{1}{5^{2}} &amp;\iff 5^{x-3}=5^{-2}\\ &amp;\iff x-3=-2 \Rightarrow x=1\end{aligned}\]
T;
$solC = <<<'T'
\[\begin{aligned}\left(\frac{1}{9}\right)^{x}=81^{x+4} &amp;\iff 9^{-x}=9^{2(x+4)}\\ &amp;\iff -x=2x+8\\ &amp;\iff 3x=-8 \Rightarrow x=-\frac{8}{3}\end{aligned}\]
T;

$S = [];

// 1. Title
$S[] = slide(null, null, '', '
  <div class="title-slide">
    <div class="chapter">
      <p>ជំពូកទី២៖ អនុគមន៍អិចស្ប៉ូណង់ស្យែល និងអនុគមន៍លោការីត</p>
      <p>មេរៀនទី១ អនុគមន៍អិចស្ប៉ូណង់ស្យែល</p>
    </div>
    <h1>សមីការអិចស្ប៉ូណង់ស្យែល</h1>
    <p class="author">' . e($author) . '</p>
  </div>');

// 2. Review
$S[] = slide(2, null, 'ដំណាក់កាលទី២ ៖ លើកមេរៀនចាស់ (១០ នាទី)',
    box('លំហាត់ ៖ ចូរដោះស្រាយសមីការខាងក្រោម',
        items(['\(2x+1=3\)', '\(x+5=0\)'], 'num') .
        '<p>គ្រូឱ្យសិស្សឡើងដោះស្រាយដាក់លើក្ដារខៀន រួចគ្រូសំយោគចម្លើយសិស្ស ។</p>', 'gold', '?') .
    box('ចម្លើយ',
        '<div class="cols"><div>\(2x+1=3\)<br>\(2x=2\)<br>\(x=1\)</div><div>\(x+5=0\)<br>\(x=-5\)</div></div>', 'mint', '✓'));

// 3. I Do: which is exponential?
$S[] = slide(3, 'I Do', '២.១ សមីការអិចស្ប៉ូណង់ស្យែល ចូលរួម',
    box('លំហាត់ ៖ ក្នុងបណ្តាសមភាពខាងក្រោម មួយណាជាសមីការអិចស្ប៉ូណង់ស្យែល ? ព្រោះអ្វី ?',
        items(['\((-3)^x=-2187\)', '\(1^x=1\)', '\(0^x=0\)', '\(3^x=2187\)']), 'gold', '?') .
    '<p class="note">គ្រូបែងចែកសិស្សជា ៤ ក្រុម រួចសរសេរលំហាត់ដាក់លើក្តារខៀន ។</p>');

// 4. We Do: discuss and explain
$S[] = slide(3, 'We Do', '២.១ សមីការអិចស្ប៉ូណង់ស្យែល ពិភាក្សា និងពន្យល់',
    box('ពិភាក្សាក្រុម (១៥ នាទី) ៖ ឆ្លើយសំណួរ និងជ្រើសរើសតំណាងក្រុម',
        '<p>សិស្សពិភាក្សាក្នុងក្រុម ឆ្លើយសំណួរ ហើយសរសេរចម្លើយរួមរបស់ក្រុម ។</p>', 'mint', '?') .
    box('ពន្យល់ (៨ នាទី)',
        '<ul class="hand"><li>គ្រូឱ្យសិស្សតំណាងក្រុមទី១ៗឡើងពន្យល់ចម្លើយរបស់ខ្លួនលើក្តារ ។</li>' .
        '<li>ឱ្យក្រុមដែលនៅសល់ត្រូវផ្ទៀងផ្ទាត់ និងបំពេញបន្ថែមចំណុចខ្វះខាត ។</li></ul>', 'mint', '?'));

// 5. We Do: answer table
$S[] = slide(3, 'We Do', 'ចម្លើយ ៖ តើមួយណាជាសមីការអិចស្ប៉ូណង់ស្យែល ?',
    box('ចម្លើយ',
        '<div class="tbl-wrap"><table class="tbl">
          <thead><tr><th>សមភាព</th><th>សមីការអិចស្ប៉ូណង់ស្យែល ?</th><th>មូលហេតុ</th></tr></thead>
          <tbody>
            <tr><td>\((-3)^x=-2187\)</td><td class="no">មិនមែន</td><td>គោល \(-3&lt;0\)</td></tr>
            <tr><td>\(1^x=1\)</td><td class="no">មិនមែន</td><td>គោល \(=1\)</td></tr>
            <tr><td>\(0^x=0\)</td><td class="no">មិនមែន</td><td>គោល \(=0\)</td></tr>
            <tr><td>\(3^x=2187\)</td><td class="yes">ជា</td><td>មាននិទស្សន្ត \(x\) ជាអញ្ញាត និងគោល \(3&gt;0,\ 3\neq 1\)</td></tr>
          </tbody></table></div>', 'mint', '✓') .
    '<p class="example">ឧទាហរណ៍ ៖ \(3^x=2187=3^7 \Rightarrow x=7\) ។</p>');

// 6. I Do: definition and general form
$S[] = slide(3, 'I Do', '២.១ សមីការអិចស្ប៉ូណង់ស្យែល ទម្រង់ទូទៅ',
    box('និយមន័យ ៖ សមីការអិចស្ប៉ូណង់ស្យែល',
        '<p>សមីការអិចស្ប៉ូណង់ស្យែល គឺជាសមីការស្វ័យគុណដែលមាន<b class="o">និទស្សន្ត</b>ជាអញ្ញាត និងមាន<b class="b">គោល</b>ជាចំនួនពិតវិជ្ជមានខុសពី 1 ។</p>', 'blue', '■') .
    box('ទម្រង់ និងវិធីដោះស្រាយ',
        '<p>សមីការទម្រង់ \(a^{f(x)}=a^{g(x)}\ (a&gt;0,\ a\neq 1)\) សមមូលនឹង \(f(x)=g(x)\) ។</p>', 'mint', '⌕'));

// 7. You Do: practice
$S[] = slide(4, 'You Do', 'ដំណាក់កាលទី៤ ៖ ពង្រឹងចំណេះដឹង (៣៥ នាទី)',
    box('លំហាត់ ៖ ចូរដោះស្រាយសមីការខាងក្រោម',
        items(['\(2^{3x+5}=128\)', '\(5^{x-3}=\dfrac{1}{25}\)', '\(\left(\dfrac{1}{9}\right)^{x}=81^{x+4}\)']) .
        '<p>គ្រូដាក់លំហាត់ឱ្យសិស្សតាមក្រុមធ្វើ រួចសរសេរចម្លើយលើក្រដាសកាតុងដើម្បីបិទលើក្តារខៀន ។</p>', 'gold', '?'));

// 8-10. You Do: solutions
$S[] = slide(4, 'You Do', 'ចម្លើយ ក. \(2^{3x+5}=128\)', box('ដំណោះស្រាយ', $solA, 'mint', '✓'));
$S[] = slide(4, 'You Do', 'ចម្លើយ ខ. \(5^{x-3}=\dfrac{1}{25}\)', box('ដំណោះស្រាយ', $solB, 'mint', '✓'));
$S[] = slide(4, 'You Do', 'ចម្លើយ គ. \(\left(\dfrac{1}{9}\right)^{x}=81^{x+4}\)', box('ដំណោះស្រាយ', $solC, 'mint', '✓'));

// 11. Worksheet
$S[] = slide(4, 'You Do', 'សន្លឹកកិច្ចការ ៖ សមីការអិចស្ប៉ូណង់ស្យែល',
    box('ផ្នែកទី១ ៖ លើកចំណេះដឹងចាស់ ដោះស្រាយ \(2x+1=3\) និង \(x+5=0\)',
        '<p>\(x=1\) និង \(x=-5\)</p>', 'mint', '?') .
    box('ផ្នែកទី២ ៖ ការរុករកចំណេះដឹងថ្មី',
        '<p>ក្នុងបណ្តាសមភាព ក. \((-3)^x=-2187\) ខ. \(1^x=1\) គ. \(0^x=0\) ឃ. \(3^x=2187\) មួយណាជាសមីការអិចស្ប៉ូណង់ស្យែល ? ព្រោះអ្វី ?</p>', 'mint', '?') .
    box('ផ្នែកទី៣ ៖ លំហាត់អនុវត្ត (ធ្វើក្នុងថ្នាក់)',
        '<p>ដោះស្រាយ ក. \(2^{3x+5}=128\) ខ. \(5^{x-3}=\dfrac{1}{25}\) គ. \(\left(\dfrac{1}{9}\right)^{x}=81^{x+4}\)</p>', 'mint', '?'));

// 12. Summary
$S[] = slide(4, null, 'សេចក្តីសង្ខេប',
    '<div class="sum sum-blue"><h3>និយមន័យ</h3><p>សមីការអិចស្ប៉ូណង់ស្យែល គឺជាសមីការដែលមាននិទស្សន្តជាអញ្ញាត និងគោលជាចំនួនពិតវិជ្ជមានខុសពី 1 ។</p></div>' .
    '<div class="sum sum-orange"><h3>វិធីដោះស្រាយ</h3><p>សរសេរអង្គទាំងពីរក្នុងគោលដូចគ្នា \(a^{f(x)}=a^{g(x)}\) រួចសន្និដ្ឋាន \(f(x)=g(x)\) ។</p></div>' .
    '<div class="sum sum-green"><h3>ឧទាហរណ៍</h3><p>\(2^{3x+5}=2^{7}\Rightarrow x=\dfrac{2}{3}\) ; \(5^{x-3}=5^{-2}\Rightarrow x=1\)</p></div>');

// 13. Homework
$S[] = slide(5, null, 'ដំណាក់កាលទី៥ ៖ កិច្ចការផ្ទះ និងបណ្តាំផ្ញើ (២ នាទី)',
    box('កិច្ចការផ្ទះ ៖ ចូរដោះស្រាយសមីការខាងក្រោម',
        items(['\(49^{x}=7^{x^{2}-15}\)', '\(36^{2x}=216^{x-1}\)']), 'gold', '?'));

// 14. Homework answers
$S[] = slide(5, null, 'ចម្លើយកិច្ចការផ្ទះ',
    box('ចម្លើយ',
        '<p>ក. \(49^{x}=7^{x^{2}-15}\iff 7^{2x}=7^{x^{2}-15}\iff x^{2}-2x-15=0\iff x=5\) ឬ \(x=-3\) ។</p>' .
        '<p>ខ. \(36^{2x}=216^{x-1}\iff 6^{4x}=6^{3(x-1)}\iff 4x=3x-3\iff x=-3\) ។</p>', 'mint', '✓'));

// 15. Conclusion
$S[] = slide(null, null, '',
    '<blockquote class="closing"><h3>សេចក្តីសន្និដ្ឋាននៃមេរៀន</h3>' .
    '<p>ដើម្បីដោះស្រាយសមីការអិចស្ប៉ូណង់ស្យែល ត្រូវសរសេរអង្គទាំងពីរក្នុងគោលដូចគ្នា រួចយកនិទស្សន្តស្មើគ្នា ។</p></blockquote>');

$total = count($S);
?>
<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(SITE_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Moul&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.css">
  <link rel="stylesheet" href="style.css">
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.js"></script>
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/contrib/auto-render.min.js"></script>
  <script defer src="script.js"></script>
  <noscript><style>.slide[hidden]{display:block}.controls{display:none}</style></noscript>
</head>
<body>

<div class="topbar">
  <span class="topbar-name"><?= e(SITE_NAME) ?></span>
  <span class="topbar-user"><?= e($user['name']) ?></span>
  <form method="post" action="logout.php">
    <?= csrf_field() ?>
    <button type="submit" class="link-btn">ចាកចេញ</button>
  </form>
</div>

<main id="deck">
<?php foreach ($S as $i => $s): ?>
  <section class="slide<?= $s['title'] === '' ? ' slide-plain' : '' ?>" id="s<?= $i + 1 ?>" <?= $i > 0 ? 'hidden' : '' ?>>
    <?php if ($s['title'] !== ''): ?>
      <header class="slide-head"><h2><?= $s['title'] ?></h2></header>
      <div class="slide-meta">
        <?php if ($s['badge']): ?>
          <span class="badge badge-<?= strtolower(str_replace(' ', '', $s['badge'])) ?>"><?= e($s['badge']) ?></span>
        <?php endif; ?>
        <?= dots($s['stage']) ?>
      </div>
    <?php endif; ?>
    <div class="slide-body"><?= $s['html'] ?></div>
  </section>
<?php endforeach; ?>
</main>

<nav class="controls" aria-label="ប្តូរស្លាយ">
  <button type="button" id="prev" class="nav-btn" aria-label="ស្លាយមុន">&#8249; មុន</button>
  <span id="counter" aria-live="polite">១ / <?= $total ?></span>
  <button type="button" id="next" class="nav-btn" aria-label="ស្លាយបន្ទាប់">បន្ទាប់ &#8250;</button>
</nav>

</body>
</html>
