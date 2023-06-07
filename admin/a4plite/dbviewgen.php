<?php

require_once "config.inc.php";
require_once "db.inc.php";

if (!isset($_POST["table"])) {

$tables = db::select("TABLE_NAME")
			->from("INFORMATION_SCHEMA.TABLES")
			->where("TABLE_TYPE = 'BASE TABLE'")
			->orderby("TABLE_NAME")
			->fetchAll();
?>
<html>
<head>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
</head>
<body>
	<form method="post">
		<?php foreach ($tables as $table) { 
				$cols = db::select("tc.CONSTRAINT_TYPE", "c.COLUMN_NAME", "c.DATA_TYPE", "c.IS_NULLABLE")
					->from(
						db::join("INFORMATION_SCHEMA.COLUMNS c")
						->leftjoin("INFORMATION_SCHEMA.KEY_COLUMN_USAGE cu")->on("c.TABLE_NAME = cu.TABLE_NAME and c.ORDINAL_POSITION = cu.ORDINAL_POSITION")
						->leftjoin("INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc")->on("c.TABLE_NAME = tc.TABLE_NAME and cu.CONSTRAINT_NAME = tc.CONSTRAINT_NAME")
					)
					->where("c.TABLE_NAME = :table")
					->orderby("c.ORDINAL_POSITION")
					->fetchAll(array(":table" => $table));

				$workflow = "";
				foreach ($cols as $col) {
					if ($col["COLUMN_NAME"] == "WORKFLOW_NUMBER")
						$workflow = "checked";
				}
		?>
			<input type="checkbox" name="table[]" value="<?= $table ?>" <?= $workflow ?>/><?= $table ?><br/>
		<?php } ?>
		<input type="submit" />
		<input type="button" value="select all" onclick="$('input:checkbox').prop('checked', true);" />
	</form>
</body>
</html>
<?php

} else {

header('Content-Type: text/plain');
?>
/****** Object:  UserDefinedFunction [dbo].[EFFECTIVE_FUND_MASTER_RID] ******/
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[EFFECTIVE_FUND_MASTER_RID]'))
DROP FUNCTION [dbo].[EFFECTIVE_FUND_MASTER_RID]
GO

CREATE FUNCTION [dbo].[EFFECTIVE_FUND_MASTER_RID] (@RID NUMERIC)
RETURNS NUMERIC AS
BEGIN
RETURN (SELECT (CASE WHEN [OLD_RID] = 0 THEN [RID] ELSE [OLD_RID] END) FROM [FUND_MASTER] WHERE [RID] = @RID)
END
GO


/****** Object:  UserDefinedFunction [dbo].[EFFECTIVE_FUND_SHARECLASS_RID] ******/
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[EFFECTIVE_FUND_SHARECLASS_RID]'))
DROP FUNCTION [dbo].[EFFECTIVE_FUND_SHARECLASS_RID]
GO

CREATE FUNCTION [dbo].[EFFECTIVE_FUND_SHARECLASS_RID] (@RID NUMERIC)
RETURNS NUMERIC AS
BEGIN
RETURN (SELECT (CASE WHEN [OLD_RID] = 0 THEN [RID] ELSE [OLD_RID] END) FROM [FUND_SHARECLASS] WHERE [RID] = @RID)
END
GO


/****** Object:  UserDefinedFunction [dbo].[EFFECTIVE_TEMPLATE_MASTER_RID] ******/
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[EFFECTIVE_TEMPLATE_MASTER_RID]'))
DROP FUNCTION [dbo].[EFFECTIVE_TEMPLATE_MASTER_RID]
GO

CREATE FUNCTION [dbo].[EFFECTIVE_TEMPLATE_MASTER_RID] (@RID NUMERIC)
RETURNS NUMERIC AS
BEGIN
RETURN (SELECT (CASE WHEN [OLD_RID] = 0 THEN [RID] ELSE [OLD_RID] END) FROM [CMS_DISCLAIMER_TEMPLATE_MASTER] WHERE [RID] = @RID)
END
GO


/****** Object:  UserDefinedFunction [dbo].[EFFECTIVE_AWARD_MASTER_RID] ******/
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[EFFECTIVE_AWARD_MASTER_RID]'))
DROP FUNCTION [dbo].[EFFECTIVE_AWARD_MASTER_RID]
GO

CREATE FUNCTION [dbo].[EFFECTIVE_AWARD_MASTER_RID] (@RID NUMERIC)
RETURNS NUMERIC AS
BEGIN
RETURN (SELECT (CASE WHEN [OLD_RID] = 0 THEN [RID] ELSE [OLD_RID] END) FROM [AWARD_MASTER] WHERE [RID] = @RID)
END
GO


/****** Object:  UserDefinedFunction [dbo].[GET_FUND_SHARECLASS_RID] ******/
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[GET_FUND_SHARECLASS_RID]'))
DROP FUNCTION [dbo].[GET_FUND_SHARECLASS_RID]
GO

CREATE FUNCTION [dbo].[GET_FUND_SHARECLASS_RID] (@FUND_SHARECLASS_RID NUMERIC, @FUND_MASTER_RID NUMERIC)
RETURNS NUMERIC AS
BEGIN
IF @FUND_SHARECLASS_RID IS NOT NULL
	RETURN @FUND_SHARECLASS_RID

RETURN (SELECT [DEFAULT_SHARE_CLASS_RID] FROM [FUND_MASTER] WHERE [RID] = @FUND_MASTER_RID)
END
GO


/****** Object:  UserDefinedFunction [dbo].[GET_FUND_SHARECLASS_RID] ******/
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[GET_FUND_MASTER_RID]'))
DROP FUNCTION [dbo].[GET_FUND_MASTER_RID]
GO

CREATE FUNCTION [dbo].[GET_FUND_MASTER_RID] (@FUND_SHARECLASS_RID NUMERIC, @FUND_MASTER_RID NUMERIC)
RETURNS NUMERIC AS
BEGIN
IF @FUND_MASTER_RID IS NOT NULL
	RETURN @FUND_MASTER_RID

RETURN (SELECT [FUND_MASTER_RID] FROM [FUND_SHARECLASS] WHERE [RID] = @FUND_SHARECLASS_RID)
END
GO


<?php
$tables = $_POST["table"];

foreach ($tables as $table) {
?>
/****** Object:  UserDefinedFunction [dbo].[<?= $table ?>_PREVIEW] ******/
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[<?= $table ?>_PREVIEW]'))
DROP FUNCTION [dbo].[<?= $table ?>_PREVIEW]
GO

CREATE FUNCTION [dbo].[<?= $table ?>_PREVIEW] (@WORKFLOW_NUMBER NUMERIC)
RETURNS TABLE AS RETURN
(
SELECT <?php
	$cols = db::select("c.COLUMN_NAME", "c.DATA_TYPE", "c.IS_NULLABLE")
		->from("INFORMATION_SCHEMA.COLUMNS c")
		->where("c.TABLE_NAME = :table")
		->orderby("c.ORDINAL_POSITION")
		->fetchAll(array(":table" => $table));

	$columns = "";
	foreach ($cols as $col)
		$columns .= "[" . $col["COLUMN_NAME"] . "], ";
	
	if (strlen($columns) > 0)
		echo substr($columns, 0, -2);

?> 
FROM [<?= $table ?>] 
WHERE 
	[STATUS] = 0 AND 
	[RID] NOT IN (SELECT [OLD_RID] FROM [<?= $table ?>] WHERE [WORKFLOW_NUMBER] = @WORKFLOW_NUMBER)

UNION ALL

SELECT (CASE WHEN [OLD_RID] = 0 THEN [RID] ELSE [OLD_RID] END) AS [RID],
<?php 
	$columns = "";
	foreach ($cols as $col) {
		if ($col["COLUMN_NAME"] == "RID")
			continue;
		if ($col["COLUMN_NAME"] == "OLD_RID" || $col["COLUMN_NAME"] == "STATUS")
			$columns .= "0 AS [" . $col["COLUMN_NAME"] . "],\n";
		else
			$columns .= "[" . $col["COLUMN_NAME"] . "],\n";
	}
	
	if (strlen($columns) > 0)
		echo substr($columns, 0, -2) . "\n";
?>
FROM [<?= $table ?>]
WHERE 
	[STATUS] = 1 AND
	[WORKFLOW_NUMBER] = @WORKFLOW_NUMBER
)
GO


<?php
}
}
?>
/****** Object:  UserDefinedFunction [dbo].[FUND_ATTRIBUTES_PREVIEW] ******/
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[FUND_ATTRIBUTES_PREVIEW]'))
DROP FUNCTION [dbo].[FUND_ATTRIBUTES_PREVIEW]
GO

CREATE FUNCTION [dbo].[FUND_ATTRIBUTES_PREVIEW] (@WORKFLOW_NUMBER NUMERIC)
RETURNS TABLE AS RETURN
(
SELECT [RID], dbo.GET_FUND_SHARECLASS_RID([FUND_SHARECLASS_RID], [FUND_MASTER_RID]) AS [FUND_SHARECLASS_RID], [ATTRIBUTE_TYPE], [ATTRIBUTE_EN], [ATTRIBUTE_TC], [ATTRIBUTE_SC], [DISP_ORDER], [CREATED_DT], [LAST_MODIFIED_DT], [ATTRIBUTE_LABEL], [AS_OF_D], [OLD_RID], [STATUS], [WORKFLOW_NUMBER], dbo.GET_FUND_MASTER_RID([FUND_SHARECLASS_RID], [FUND_MASTER_RID]) AS [FUND_MASTER_RID]
FROM [FUND_ATTRIBUTES_PREVIEW_INTERNAL](@WORKFLOW_NUMBER)
)
GO


