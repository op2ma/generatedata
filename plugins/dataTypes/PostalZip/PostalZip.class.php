<?php

class DataType_PostalZip extends DataTypePlugin {
	protected $dataTypeName = "Postal / Zip";
	protected $dataTypeFieldGroup = "geo";
	protected $dataTypeFieldGroupOrder = 30;
	protected $processOrder = 2;
	protected $jsModules = array("PostalZip.js");

	private $helpDialogWidth = 370;
	private $zipFormats;


	//$g_countries = gd_get_configurable_countries();

	public function __construct($runtimeContext) {
		parent::__construct($runtimeContext);

		// if we're in the process of generating data, populate the private vars with the first and last names
		// needed for data generation
		if ($runtimeContext == "generation") {
			self::initZipFormats();
		}
	}


	public function generate($generator, $generationContextData) {
		$options = $generationContextData["generationOptions"];

		// track the country info (this finds the FIRST country field listed)
		$rowCountryInfo = array();
		/*while (list($key, $info) = each($existing_row_data)) {
			if ($info["data_type_folder"] == "Country") {
				$PostalZip_row_country_info = $info;
				break;
			}
		}*/

		$randomZip = "";
		if (empty($rowCountryInfo)) {
			$randCountry = $options[rand(0, count($options)-1)];
			$randomZip = $this->convert($this->formats[$randCountry]);
		} else {
			// if this country is one of the formats that was selected, generate it in that format -
			// otherwise just generate a zip in any selected format
			$countrySlug = $rowCountryInfo["randomData"]["slug"];

			if (in_array($countrySlug, $options)) {
				$randomZip = $this->convert($this->formats[$countrySlug]);
			} else {
				$randCountry = $options[rand(0, count($options)-1)];
				$randomZip = $this->convert($this->formats[$randCountry]);
			}
		}

		return $randomZip;
	}

	public function getRowGenerationOptions($generator, $postdata, $column, $numCols) {
		// dtCountryIncludeZip_canada_1

		$countries = $postdata["gdCountries"];
		$options = array();
		foreach ($countries as $slug) {
			if (isset($postdata["dtIncludeZip_{$slug}_$col"])) {
				$options[] = $slug;
			}
		}

		// dtCountryIncludeZip_canada_1

		return $options;
	}

	public function getOptionsColumnHTML() {
		$countryPlugins = Core::$countryPlugins;

		$html = "";
		foreach ($countryPlugins as $pluginInfo) {
			$slug       = $pluginInfo->getSlug();
			$regionName = $pluginInfo->getRegionNames();

			$html .= <<<EOF
<div class="dtCountry dtCountry_$slug">
	<input type="checkbox" name="dtCountryIncludeZip_{$slug}_%ROW%" id="dtCountryIncludeZip_{$slug}_%ROW%" checked="checked" />
	<label for="dtCountryIncludeZip_{$slug}_%ROW%">$regionName</label>
</div>
EOF;
		}
		return $html;
	}

	public function getHelpDialogInfo() {
		return array(
			"dialogWidth" => $this->helpDialogWidth,
			"content"     => "<p>{$this->L["PostalZip_help_text"]}</p>"
		);
	}


	private function initZipFormats() {
		$countryPlugins = Core::$countryPlugins;

		$formats = array();
		foreach ($countryPlugins as $countryInfo) {
			$countrySlug = $countryInfo->getSlug();
			$zipFormat   = $countryInfo->getZipFormat();
			$formats[$countrySlug] = $zipFormat;
		}

		$this->zipFormats = $formats;
	}


	private function convert($str) {
		$formats = explode("|", $str);
		if (count($formats) == 1) {
			$format = $formats[0];
		} else {
			$format = $formats[rand(0, count($formats)-1)];
		}

		return gd_generate_random_alphanumeric_str($format);
	}


	public function getExportTypeInfo($exportType, $options) {
		$info = "";
		switch ($export_type) {
			case "sql":
				if ($options == "MySQL" || $options == "SQLite") {
					$info = "varchar(10) default NULL";
				} else if ($options == "Oracle") {
					$info = "varchar2(10) default NULL";
				}
				break;
		}

		return $info;
	}
}