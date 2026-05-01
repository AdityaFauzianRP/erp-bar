<?php

$files = glob("app/Filament/Resources/*Report*/*ReportResource.php");
foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, '$shouldRegisterNavigation') === false) {
        $content = str_replace(
            "protected static ?string \$modelLabel",
            "protected static bool \$shouldRegisterNavigation = false;\n    protected static ?string \$modelLabel",
            $content
        );
        $content = str_replace(
            "protected static string|\UnitEnum|null \$navigationGroup = 'Laporan';",
            "protected static string|\UnitEnum|null \$navigationGroup = 'Laporan';\n    protected static bool \$shouldRegisterNavigation = false;",
            $content
        );
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
