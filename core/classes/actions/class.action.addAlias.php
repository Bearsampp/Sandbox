<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionAddAlias
 * Handles the creation of a new alias in the Bearsampp application.
 */
class ActionAddAlias
{
    /**
     * @var object The main window object created by WinBinder.
     */
    private object $wbWindow;

    /**
     * @var object The label for the alias name input field.
     */
    private object $wbLabelName;

    /**
     * @var object The input field for the alias name.
     */
    private object $wbInputName;

    /**
     * @var object The label for the destination path input field.
     */
    private object $wbLabelDest;

    /**
     * @var object The input field for the destination path.
     */
    private object $wbInputDest;

    /**
     * @var object The button to browse for the destination path.
     */
    private object $wbBtnDest;

    /**
     * @var object The label for the explanation text.
     */
    private object $wbLabelExp;

    /**
     * @var object The progress bar to show the progress of adding the alias.
     */
    private object $wbProgressBar;

    /**
     * @var object The save button to confirm the alias creation.
     */
    private object $wbBtnSave;

    /**
     * @var object The cancel button to abort the alias creation process.
     */
    private object $wbBtnCancel;

    const GAUGE_SAVE = 2;

    /**
     * ActionAddAlias constructor.
     * Initializes the window and its controls for adding a new alias.
     *
     * @param array $args Arguments passed to the constructor.
     */
    public function __construct(array $args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        $initName = 'test';
        $initDest = 'C:\\';
        $apachePortUri = $bearsamppBins->getApache()->getPort() !== 80 ? ':' . $bearsamppBins->getApache()->getPort() : '';

        $bearsamppWinbinder->reset();
        $this->wbWindow = $bearsamppWinbinder->createAppWindow(
            $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE), 490, 200, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP
        );

        $this->wbLabelName = $bearsamppWinbinder->createLabel(
            $this->wbWindow, $bearsamppLang->getValue(Lang::ALIAS_NAME_LABEL) . ' :', 15, 15, 85, null, WBC_RIGHT
        );
        $this->wbInputName = $bearsamppWinbinder->createInputText(
            $this->wbWindow, $initName, 105, 13, 150, null
        );

        $this->wbLabelDest = $bearsamppWinbinder->createLabel(
            $this->wbWindow, $bearsamppLang->getValue(Lang::ALIAS_DEST_LABEL) . ' :', 15, 45, 85, null, WBC_RIGHT
        );
        $this->wbInputDest = $bearsamppWinbinder->createInputText(
            $this->wbWindow, $initDest, 105, 43, 190, null, null, WBC_READONLY
        );
        $this->wbBtnDest = $bearsamppWinbinder->createButton(
            $this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_BROWSE), 300, 43, 110
        );

        $this->wbLabelExp = $bearsamppWinbinder->createLabel(
            $this->wbWindow, sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $initName, $initDest), 15, 80, 470, 50
        );

        $this->wbProgressBar = $bearsamppWinbinder->createProgressBar(
            $this->wbWindow, self::GAUGE_SAVE + 1, 15, 137, 275
        );
        $this->wbBtnSave = $bearsamppWinbinder->createButton(
            $this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_SAVE), 300, 132
        );
        $this->wbBtnCancel = $bearsamppWinbinder->createButton(
            $this->wbWindow, $bearsamppLang->getValue(Lang::BUTTON_CANCEL), 387, 132
        );

        $bearsamppWinbinder->setHandler($this->wbWindow, $this, 'processWindow');
        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Processes window events and handles user interactions.
     *
     * @param object $window The window object.
     * @param int $id The control ID.
     * @param object $ctrl The control object.
     * @param mixed $param1 Additional parameter 1.
     * @param mixed $param2 Additional parameter 2.
     */
    public function processWindow(object $window, int $id, object $ctrl, mixed $param1, mixed $param2): void
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppLang, $bearsamppWinbinder;

        $apachePortUri = $bearsamppBins->getApache()->getPort() !== 80 ? ':' . $bearsamppBins->getApache()->getPort() : '';
        $aliasName = $bearsamppWinbinder->getText($this->wbInputName[WinBinder::CTRL_OBJ]);
        $aliasDest = $bearsamppWinbinder->getText($this->wbInputDest[WinBinder::CTRL_OBJ]);

        switch ($id) {
            case $this->wbInputName[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->setText(
                    $this->wbLabelExp[WinBinder::CTRL_OBJ],
                    sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $aliasName, $aliasDest)
                );
                $bearsamppWinbinder->setEnabled($this->wbBtnSave[WinBinder::CTRL_OBJ], !empty($aliasName));
                break;
            case $this->wbBtnDest[WinBinder::CTRL_ID]:
                $aliasDest = $bearsamppWinbinder->sysDlgPath($window, $bearsamppLang->getValue(Lang::ALIAS_DEST_PATH), $aliasDest);
                if ($aliasDest && is_dir($aliasDest)) {
                    $bearsamppWinbinder->setText($this->wbInputDest[WinBinder::CTRL_OBJ], $aliasDest . '\\');
                    $bearsamppWinbinder->setText(
                        $this->wbLabelExp[WinBinder::CTRL_OBJ],
                        sprintf($bearsamppLang->getValue(Lang::ALIAS_EXP_LABEL), $apachePortUri, $aliasName, $aliasDest . '\\')
                    );
                }
                break;
            case $this->wbBtnSave[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->setProgressBarMax($this->wbProgressBar, self::GAUGE_SAVE + 1);
                $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                if (!ctype_alnum($aliasName)) {
                    $bearsamppWinbinder->messageBoxError(
                        sprintf($bearsamppLang->getValue(Lang::ALIAS_NOT_VALID_ALPHA), $aliasName),
                        $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE)
                    );
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    break;
                }

                if (is_file($bearsamppRoot->getAliasPath() . '/' . $aliasName . '.conf')) {
                    $bearsamppWinbinder->messageBoxError(
                        sprintf($bearsamppLang->getValue(Lang::ALIAS_ALREADY_EXISTS), $aliasName),
                        $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE)
                    );
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                    break;
                }

                if (file_put_contents($bearsamppRoot->getAliasPath() . '/' . $aliasName . '.conf', $bearsamppBins->getApache()->getAliasContent($aliasName, $aliasDest)) !== false) {
                    $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                    $bearsamppBins->getApache()->getService()->restart();
                    $bearsamppWinbinder->incrProgressBar($this->wbProgressBar);

                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf($bearsamppLang->getValue(Lang::ALIAS_CREATED), $aliasName, $apachePortUri, $aliasName, $aliasDest),
                        $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE)
                    );
                    $bearsamppWinbinder->destroyWindow($window);
                } else {
                    $bearsamppWinbinder->messageBoxError(
                        $bearsamppLang->getValue(Lang::ALIAS_CREATED_ERROR),
                        $bearsamppLang->getValue(Lang::ADD_ALIAS_TITLE)
                    );
                    $bearsamppWinbinder->resetProgressBar($this->wbProgressBar);
                }
                break;
            case IDCLOSE:
            case $this->wbBtnCancel[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }
}
