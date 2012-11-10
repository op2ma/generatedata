<?php


/**
 * Our base class for all Export Type plugins. All Export Types must define a class that extends this class.
 * This page documents and defines (where the language permits!) what's required, what's optional, and
 * what each method and member variable does.
 */
abstract class ExportTypePlugin {

	/**
	 * The name of the export type "HTML", "XML" etc. This is always in English; even in different languages,
	 * "JSON" is still "JSON", so having no translation is acceptable here.
	 * @var string
	 */
	protected $exportTypeName = "";

	/**
	 * An array of JS modules that need to be included for this module. They should be requireJS-friendly
	 * modules.
	 * @var array
	 */
	protected $jsModules = array();

	/**
	 * By default, the Data Generator opens a dialog window to display the results. This setting lets you choose
	 * to override that and just pass the Ajax request directly to the server. This is handy for export types like
	 * Excel which just prompts for file download - not shows the results in the UI.
	 * @var boolean
	 */
	protected $openResultsInDialog = true;

	/**
	 * This controls
	 * @var unknown_type
	 */
	protected $syntaxHighlighterLanguage = null;

	/**
	 * Contains all strings for the current language. This is populated automatically on instantiation and
	 * contains the strings for the currently selected language.
	 * @var array
	 */
	public $L = array();


	/**
	 * This does the job of actually generating the data in the appropriate format. It's fed the instantiated
	 * Generator class, containing the various information the Export Type could need.
	 *
	 * @param Generator $generator
	 * @return
	 */
	abstract function generate($generator);


	// 2. OPTIONALLY DEFINED FUNCTIONS

	/**
	 * Our default constructor. This populates $L for the instantiated class. Export Types
	 * are constructed when the main generator UI page loads; for actual code generation, their generate()
	 * function is called.
	 */
	public function __construct($runtimeContext) {

		// a little magic to find the current instantiated class's folder
		$currClass = new ReflectionClass(get_class($this));
		$currClassFolder = dirname($currClass->getFileName());

		$defaultLangFileStr = Core::getDefaultLanguageFile();
		$currentLangFileStr = Core::$language->getCurrentLanguageFile();

		$currentLangFile = $currClassFolder . "/lang/" . $currentLangFileStr . ".php";
		$defaultLangFile = $currClassFolder . "/lang/" . $defaultLangFileStr . ".php";

		if (file_exists($currentLangFile)) {
			require($currentLangFile);
		} else if (file_exists($defaultLangFile)) {
			require($defaultLangFile);
		}

		if (isset($L)) {
			$this->L = $L;
		}
	}

	/**
	 * This is called once during the initial installation of the script, or when the installation is reset (which is
	 * effectively a fresh install). It is called AFTER the Core tables are installed, and you can rely
	 * on Core::$db having been initialized and the database connection having been set up.
	 *
	 * @return array [0] success / Error
	 * 				 [1] the error message, if there was a problem
	 */
	static function install() {
		return array(true, "");
	}

	/**
	 * Outputs any additional headers, prior to the generator() call.
	 */
	public function outputHeaders() {
		return;
	}

	/**
	 * If the Export Type needs to display any additional settings in the UI (like XML, CSV or SQL does), it needs
	 * to define this function which return the markup. The hiding/showing of the appropriate section happens automatically.
	 * @return string
	 */
	public function getAdditionalSettingsHTML() {
		return "";
	}

	// 3. NON-OVERRIDABLE FUNCTIONS

	/**
	 * Returns a list of all javascript modules for this Export Type.
	 * @return array
	 */
	public final function getJSModules() {
		return $this->jsModules;
	}

	/**
	 * Returns the name of the Export Type in the current language.
	 * @return string
	 */
	public final function getName() {
		return $this->exportTypeName;
	}

	/**
	 * Returns the name of the Export Type in the current language.
	 * @return string
	 */
	public final function getFolder() {
		return $this->folder;
	}

	/**
	 * Returns the name of the Export Type in the current language.
	 * @return string
	 */
	public final function getRowLabelTranslationKey() {
		return $this->rowLabelTranslationKey;
	}

	/**
	 * Returns the path from the generatedata root folder. That value is automatically created
	 * for each module when it it instantiated.
	 * @return string
	 */
	public final function getPath() {
		return $this->path;
	}
}