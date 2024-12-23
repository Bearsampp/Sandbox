<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionChangeBrowser
 *
 * This class handles the action of changing the default browser in the Bearsampp application.
 * It creates a window with options to select from installed browsers or browse for a different browser executable.
 * The selected browser is then saved in the configuration.
 */
class ActionChangeBrowser
{
    /**
     * @var object The main window object created by WinBinder.
     */
    private object $wbWindow;

    /**
     * @var object The label for the explanation text.
     */
    private object $wbLabelExp;

    /**
     * @var array The radio buttons for selecting browsers.
     */
    private array $wbRadioButton;

    /**
     * @var object The radio button for selecting a custom browser.
     */
    private object $wbRadioButtonOther;

    /**
     * @var object The input field for browsing to a custom browser executable.
     */
    private object $wbInputBrowse;

    /**
     * @var object The button to open the file dialog for browsing.
     */
    private object $wbBtnBrowse;

    /**
     * @var object The progress bar to show the progress of saving the browser selection.
     */
    private object $wbProgressBar;

    /**
     * @var object The save button to confirm the browser selection.
     */
    private object $wbBtnSave;

    /**
     * @var object The cancel button to abort the browser selection process.
     */
    private object $wbBtnCancel;

    const GAUGE_SAVE = 2;

    /**
     * Constructor for ActionChangeBrowser.
     *
     * Initializes the window and its components, sets up event handlers, and starts the main loop.
     *
     * @param array $args Arguments passed to the constructor.
     */
    public function __construct(array $args)
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppWinbinder;

        $bearsamppWinbinder->reset();
        $this->wbWindow = $bearsamppWinbinder->createAppWindow(
            $bearsamppLang->getValue(Lang::CHANGE_BROWSER_TITLE), 490, 350, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP
        );

        $this->wbLabelExp = $bearsamppWinbinder->createLabel(
            $this->wbWindow, $bearsamppLang->getValue(Lang::CHANGE_BROWSER_EXP_LABEL), 15, 15, 470, 50
        );

        $currentBrowser = $bearsamppConfig->getBrowser();
        $this->wbRadioButton[] = $bearsamppWinbinder->createRadioButton(
            $this->wbWindow, $currentBrowser, true, 15, 40, 470, 20, true
        );

        $yPos = 70;
        $installedBrowsers = Vbs::getInstalledBrowsers();
        foreach ($installedBrowsers as $installedBrowser) {
            if ($installedBrowser !== $currentBrowser) {
                $this->wbRadioButton[] = $bearsamppWinbinder->createRadioButton(
                    $this->wbWindow, $installedBrowser, false, 15, $yPos, 470, 20
                );
                $yPos += 30;
            }
        }

        $this->wbRadioButtonOther = $bearsamppWinbinder->createRadioButton(
            $this->wbWindow, $bearsamppLang->getValue(Lang::CHANGE_BROWSER_OTHER_LABEL), false, 15, $yPos, 470, 15
        );

        $this->wbInputBrowse = $bearsamppWinbinder->createInputText(
            $this->wbWindow, null, 30, $yPos + 30, 190, null, 20, WBC_READONLY
        );
        $this->wbBtnBrowse = $bearsamppWinbinder->createButton(
            $this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_BROWSE), 225, $yPos + 25, 110
        );
        $bearsamppWinbinder->setEnabled($this->wbBtnBrowse[WinBinder::CTRL_OBJ], false);

        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar(
            $this->wbWindow, self::GAUGE_SAVE, 15, 287, 275
        );
        $this->wbBtnSave = $bearsamppWinbinder->createButton(
            $this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_SAVE), 300, 282
        );
        $this->wbBtnCancel = $bearsamppWinbinder->createButton(
            $this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_CANCEL), 387, 282
        );
        $bearsamppWinbinder->setEnabled($this->wbBtnSave[WinBinder::CTRL_OBJ], !empty($currentBrowser));

        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Processes window events.
     *
     * Handles the logic for various controls in the window, such as enabling/disabling buttons,
     * opening file dialogs, and saving the selected browser.
     *
     * @param object $window The window object.
     * @param int $id The ID of the control that triggered the event.
     * @param object $ctrl The control object.
     * @param mixed $param1 Additional parameter 1.
     * @param mixed $param2 Additional parameter 2.
     */
    public function processWindow(object $window, int $id, object $ctrl, mixed $param1, mixed $param2): void
    {
        global $bearsamppConfig, $bearsamppLang, $bearsamppWinbinder;

        // Get other value
        $browserPath = $bearsamppWinbinder->getText($this->wbInputBrowse[WinBinder::CTRL_OBJ]);

        // Get value
        $selected = null;
        if ($bearsamppWinbinder->getValue($this->wbRadioButtonOther[WinBinder::CTRL_OBJ]) === 1) {
            $bearsamppWinbinder->setEnabled($this->wbBtnBrowse[WinBinder::CTRL_OBJ], true);
            $selected = $bearsamppWinbinder->getText($this->wbInputBrowse[WinBinder::CTRL_OBJ]);
        } else {
            $bearsamppWinbinder->setEnabled($this->wbBtnBrowse[WinBinder::CTRL_OBJ], false);
        }
        foreach ($this->wbRadioButton as $radioButton) {
            if ($bearsamppWinbinder->getValue($radioButton[WinBinder::CTRL_OBJ]) === 1) {
                $selected = $bearsamppWinbinder->getText($radioButton[WinBinder::CTRL_OBJ]);
                break;
            }
        }

        // Enable/disable save button
        $bearsamppWinbinder->setEnabled($this->wbBtnSave[WinBinder::CTRL_OBJ], !empty($selected));

        switch ($id) {
            case $this->wbBtnBrowse[WinBinder::CTRL_ID]:
                $browserPath = trim($bearsamppWinbinder->sysDlgOpen(
                    $window,
                    $bearsamppLang->getValue(Lang::ALIAS_DEST_PATH),
                    [['Executable', '*.exe']],
                    $browserPath
                ));
                if ($browserPath && is_file($browserPath)) {
                    $bearsamppWinbinder->setText($this->wbInputBrowse[WinBinder::CTRL_OBJ], $browserPath);
                }
                break;
            case $this->wbBtnSave[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);
                $bearsamppConfig->replace(Config::CFG_BROWSER, $selected);

                $bearsamppWinbinder->messageBoxInfo(
                    sprintf($bearsamppLang->getValue(Lang::CHANGE_BROWSER_OK), $selected),
                    $bearsamppLang->getValue(Lang::CHANGE_BROWSER_TITLE)
                );
                $bearsamppWinbinder->destroyWindow($window);

                break;
            case IDCLOSE:
            case $this->wbBtnCancel[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}
