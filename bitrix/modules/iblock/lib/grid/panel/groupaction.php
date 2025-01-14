<?php
namespace Bitrix\Iblock\Grid\Panel;

use Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class GroupAction
{
	private const ELEMENT_LIST = 'E';
	private const SECTION_LIST = 'S';
	private const MIXED_LIST = 'M';
	private const SUBELEMENT_LIST = 'O';

	private const PREFIX_ID = 'iblock_grid_action_';

	/** @var string Grid Id */
	protected $entityId = '';

	/** @var array */
	protected $options = [];

	/** @var int */
	protected $iblockId = null;

	/** @var array */
	protected $iblockConfig = [
		'SECTIONS' => 'N',
		'SECTION_CHOOSER' => Iblock\IblockTable::SECTION_CHOOSER_SELECT
	];

	/** @var Main\Grid\Panel\Snippet */
	protected $mainSnippet = null;

	/** @var Main\HttpRequest */
	protected $request = null;

	/** @var array */
	protected $sections = null;

	protected $actionHandlers = [];

	public function __construct(array $options)
	{
		$this->entityId = $options['ENTITY_ID'];
		$this->iblockId = $options['IBLOCK_ID'];
		$this->options = $options;

		$this->mainSnippet = new Main\Grid\Panel\Snippet();
		$this->request = Main\Context::getCurrent()->getRequest();

		$this->initConfig();

		$this->initActions();
	}

	/**
	 * @param array $actions
	 * @return array
	 */
	public function getList(array $actions)
	{
		$result = [];
		if (!empty($actions))
		{
			foreach ($actions as $code => $params)
			{
				if (is_string($params))
				{
					$code = $params;
					$params = [];
				}
				if (is_string($code) && is_array($params))
				{
					$row = $this->get($code, $params);
					if ($row !== null)
					{
						$result[$code] = $row;
					}
				}
			}
			unset($row, $code, $params);
		}
		return $result;
	}

	/**
	 * @param string $code
	 * @param array $params
	 * @return array|null
	 */
	public function get(string $code, array $params = [])
	{
		$code = trim($code);
		if ($code === '' || !isset($this->actionHandlers[$code]))
			return null;

		$method = 'action'.$this->actionHandlers[$code].'Panel';
		if (is_callable([$this, $method]))
		{
			return call_user_func_array([$this, $method], [$params]);
		}

		return [];
	}

	/**
	 * @param string $code
	 * @return array|null
	 */
	public function getRequest(string $code)
	{
		$code = trim($code);
		if ($code === '' || !isset($this->actionHandlers[$code]))
			return null;

		$method = 'action'.$this->actionHandlers[$code].'Request';
		if (is_callable([$this, $method]))
		{
			return call_user_func_array([$this, $method], []);
		}

		return [];
	}

	/**
	 * @return string
	 */
	protected function getEntityId()
	{
		return $this->entityId;
	}

	/**
	 * @return void
	 */
	protected function initConfig()
	{
		$iterator = Iblock\IblockTable::getList([
			'select' => ['ID', 'SECTION_CHOOSER', 'SECTIONS' => 'TYPE.SECTIONS'],
			'filter' => ['=ID' => $this->iblockId]
		]);
		$row = $iterator->fetch();
		if (!empty($row))
		{
			$this->iblockConfig['SECTIONS'] = $row['SECTIONS'];
			$this->iblockConfig['SECTION_CHOOSER'] = $row['SECTION_CHOOSER'];
		}
		unset($row, $iterator);
	}

	/**
	 * @return void
	 */
	protected function initActions()
	{
		$this->actionHandlers = $this->getActionHandlers();
	}

	/**
	 * @return array
	 */
	protected function getActionHandlers()
	{
		$result = [];

		$result[Iblock\Grid\ActionType::EDIT] = 'Edit';
		$result[Iblock\Grid\ActionType::SELECT_ALL] = 'SelectAll';
		$result[Iblock\Grid\ActionType::DELETE] = 'Delete';
		$result[Iblock\Grid\ActionType::ACTIVATE] = 'Activate';
		$result[Iblock\Grid\ActionType::DEACTIVATE] = 'Deactivate';
		$result[Iblock\Grid\ActionType::CLEAR_COUNTER] = 'ClearCounter';
		$result[Iblock\Grid\ActionType::CODE_TRANSLIT] = 'CodeTranslit';
		$result[Iblock\Grid\ActionType::MOVE_TO_SECTION] = 'AdjustSection';
		$result[Iblock\Grid\ActionType::ADD_TO_SECTION] = 'AddSection';
		$result[Iblock\Grid\ActionType::ELEMENT_UNLOCK] = 'ElementUnlock';
		$result[Iblock\Grid\ActionType::ELEMENT_LOCK] = 'ElementLock';
		$result[Iblock\Grid\ActionType::ELEMENT_WORKFLOW_STATUS] = 'ElementWorkflowStatus';

		return $result;
	}

	/**
	 * @return array
	 */
	protected function getDefaultApplyAction()
	{
		return ['JS' => "BX.adminUiList.SendSelected('{$this->getEntityId()}')"];
	}

	/**
	 * @param string $id
	 * @return string
	 */
	protected function getElementId($id)
	{
		return self::PREFIX_ID.$this->getEntityId().'_'.$id;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	protected function getApplyButton(array $params)
	{
		$result = $this->mainSnippet->getApplyButton([]);
		$result['id'] = $this->getElementId($params['APPLY_BUTTON_ID']);
		$this->mainSnippet->setButtonActions(
			$result,
			[
				[
					'ACTION' => Main\Grid\Panel\Actions::CALLBACK,
					'DATA' => [
						$this->getDefaultApplyAction()
					]
				]
			]
		);
		return $result;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	protected function getApplyButtonWithConfirm(array $params)
	{
		$result = $this->mainSnippet->getApplyButton([]);
		$result['id'] = $this->getElementId($params['APPLY_BUTTON_ID']);
		$this->mainSnippet->setButtonActions(
			$result,
			[
				[
					'ACTION' => Main\Grid\Panel\Actions::CALLBACK,
					'CONFIRM' => true,
					'CONFIRM_MESSAGE' => (isset($params['CONFIRM_MESSAGE']) && $params['CONFIRM_MESSAGE'] != ''
						? $params['CONFIRM_MESSAGE']
						: $params['DEFAULT_CONFIRM_MESSAGE']
					),
					'DATA' => [
						$this->getDefaultApplyAction()
					]
				]
			]
		);
		return $result;
	}

	/**
	 * @return void
	 */
	protected function loadSections()
	{
		if ($this->sections === null)
		{
			$this->sections = [];
			if ($this->iblockId > 0)
			{
				$iterator = \CIBlockSection::getTreeList(
					['IBLOCK_ID' => $this->iblockId],
					['ID', 'NAME', 'DEPTH_LEVEL', 'LEFT_MARGIN']
				);
				while ($row = $iterator->Fetch())
				{
					$this->sections[] = [
						'NAME' => str_repeat(' . ', $row['DEPTH_LEVEL']).$row['NAME'],
						'VALUE' => $row['ID']
					];
				}
				unset($row, $iterator);
			}
		}
	}

	/**
	 * @param bool $addTop
	 * @return array
	 */
	protected function getSections($addTop = false)
	{
		$addTop = (bool)$addTop;
		$this->loadSections();
		$result = $this->sections;
		if ($addTop)
		{
			$result = array_merge(
				[
					['NAME' => Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_MESS_SECTION_TOP_LEVEL'), 'VALUE' => '0']
				],
				$result
			);
		}
		return $result;
	}

	/**
	 * @param array $action
	 * @return array
	 */
	protected function getAddSectionList(array $action)
	{
		return [
			'name' => $action['NAME'],
			'type' => 'multicontrol',
			'action' => [
				[
					'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
				],
				[
					'ACTION' => Main\Grid\Panel\Actions::CREATE,
					'DATA' => [
						[
							'TYPE' => Main\Grid\Panel\Types::DROPDOWN,
							'ID' => $this->getElementId($action['SECTION_LIST_ID']),
							'NAME' => 'section_to_move',
							'ITEMS' => $this->getSections(false)
						],
						$this->getApplyButton($action)
					]
				]
			]
		];
	}

	/**
	 * @param array $action
	 * @return array
	 */
	protected function getAddSectionDialog(array $action)
	{
		return [
			'name' => $action['NAME'],
			'type' => 'multicontrol',
			'action' => [
				[
					'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
				],
				[
					'ACTION' => Main\Grid\Panel\Actions::CREATE,
					'DATA' => [
						[
							'TYPE' => Main\Grid\Panel\Types::TEXT,
							'ID' => '',
							'NAME' => '',
							'TITLE' => '12141'
						],
						/*[
							'TYPE' => Main\Grid\Panel\Types::DATE,
							'ID' => '',
							'NAME' => ''
						], */
						[
							'TYPE' => Main\Grid\Panel\Types::BUTTON,
							'ID' => '',
							'NAME' => ''
						],
						$this->getApplyButton($action)
					]
				]
			]
		];
	}

	/**
	 * @param array $action
	 * @return array
	 */
	protected function getAdjustSectionList(array $action)
	{
		return [
			'name' => $action['NAME'],
			'type' => 'multicontrol',
			'action' => [
				[
					'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
				],
				[
					'ACTION' => Main\Grid\Panel\Actions::CREATE,
					'DATA' => [
						[
							'TYPE' => Main\Grid\Panel\Types::DROPDOWN,
							'ID' => $this->getElementId($action['SECTION_LIST_ID']),
							'NAME' => 'section_to_move',
							'ITEMS' => $this->getSections(true)
						],
						$this->getApplyButton($action)
					]
				]
			]
		];
	}

	/**
	 * @param array $params
	 * @return string
	 */
	protected function actionEditPanel(array $params = [])
	{
		return (isset($params['NAME']) && $params['NAME'] != ''
			? $params['NAME']
			: Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_EDIT')
		);
	}

	/**
	 * @param array $params
	 * @return true
	 */
	protected function actionSelectAllPanel(array $params = [])
	{
		return true;
	}

	/**
	 * @param array $params
	 * @return string
	 */
	protected function actionDeletePanel(array $params = [])
	{
		return (isset($params['NAME']) && $params['NAME'] != ''
			? $params['NAME']
			: Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_DELETE')
		);
	}

	/**
	 * @param array $params
	 * @return string
	 */
	protected function actionActivatePanel(array $params = [])
	{
		return (isset($params['NAME']) && $params['NAME'] != ''
			? $params['NAME']
			: Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_ACTIVATE')
		);
	}

	/**
	 * @param array $params
	 * @return string
	 */
	protected function actionDeactivatePanel(array $params = [])
	{
		return (isset($params['NAME']) && $params['NAME'] != ''
			? $params['NAME']
			: Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_DEACTIVATE')
		);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	protected function actionClearCounterPanel(array $params = [])
	{
		$name = (isset($params['NAME']) && $params['NAME'] != ''
			? $params['NAME']
			: Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_CLEAR_COUNTER')
		);

		$params['APPLY_BUTTON_ID'] = 'clear_counter_confirm';
		$params['DEFAULT_CONFIRM_MESSAGE'] = Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_CLEAR_COUNTER_CONFIRM');

		return [
			'name' => $name,
			'type' => 'multicontrol',
			'action' => [
				[
					'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
				],
				[
					'ACTION' => Main\Grid\Panel\Actions::CREATE,
					'DATA' => [ $this->getApplyButtonWithConfirm($params) ]
				]
			]
		];
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function actionCodeTranslitPanel(array $params = [])
	{
		$name = (isset($params['NAME']) && $params['NAME'] != ''
			? $params['NAME']
			: Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_CODE_TRANSLITERATION')
		);

		$params['APPLY_BUTTON_ID'] = 'code_translit_confirm';
		$params['DEFAULT_CONFIRM_MESSAGE'] = Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_CODE_TRANSLITERATION_CONFIRM');

		return [
			'name' => $name,
			'type' => 'multicontrol',
			'action' => [
				[
					'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
				],
				[
					'ACTION' => Main\Grid\Panel\Actions::CREATE,
					'DATA' => [ $this->getApplyButtonWithConfirm($params) ]
				]
			]
		];
	}

	/**
	 * @param array $params
	 * @return array|null
	 */
	protected function actionAdjustSectionPanel(array $params = [])
	{
		if ($this->iblockConfig['SECTIONS'] != 'Y')
		{
			return null;
		}
		if (!isset($params['NAME']) || $params['NAME'] == '')
		{
			$params['NAME'] = Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_ADJUST_SECTION');
		}

		$params['APPLY_BUTTON_ID'] = 'send_adjust_list';
		if ($this->iblockConfig['SECTION_CHOOSER'] == Iblock\IblockTable::SECTION_CHOOSER_PATH)
		{
			$params['SECTION_LIST_ID'] = 'set_sections';
			return $this->getAdjustSectionList($params);
		}
		else
		{
			$params['SECTION_LIST_ID'] = 'set_sections';
			return $this->getAdjustSectionList($params);
		}
	}

	/**
	 * @return array|null
	 */
	protected function actionAdjustSectionRequest()
	{
		$sectionId = $this->request->get('section_to_move');
		return (is_string($sectionId) ? ['SECTION_ID' => $sectionId] : null);
	}

	/**
	 * @param array $params
	 * @return array|null
	 */
	protected function actionAddSectionPanel(array $params = [])
	{
		if ($this->iblockConfig['SECTIONS'] != 'Y')
		{
			return null;
		}
		if (!isset($params['NAME']) || $params['NAME'] == '')
		{
			$params['NAME'] = Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_ADD_SECTION');
		}

		$params['APPLY_BUTTON_ID'] = 'send_add_list';
		if ($this->iblockConfig['SECTION_CHOOSER'] == Iblock\IblockTable::SECTION_CHOOSER_PATH)
		{
			$params['SECTION_LIST_ID'] = 'additional_sections';
			return $this->getAddSectionList($params);
		}
		else
		{
			$params['SECTION_LIST_ID'] = 'additional_sections';
			return $this->getAddSectionList($params);
		}
	}

	/**
	 * @return array|null
	 */
	protected function actionAddSectionRequest()
	{
		$sectionId = $this->request->get('section_to_move');
		return (is_string($sectionId) ? ['SECTION_ID' => $sectionId] : null);
	}

	/**
	 * @param array $params
	 * @return string
	 */
	protected function actionElementUnlockPanel(array $params = [])
	{
		return (isset($params['NAME']) && $params['NAME'] != ''
			? $params['NAME']
			: Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_ELEMENT_UNLOCK')
		);
	}

	/**
	 * @param array $params
	 * @return string
	 */
	protected function actionElementLockPanel(array $params = [])
	{
		return (isset($params['NAME']) && $params['NAME'] != ''
			? $params['NAME']
			: Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_ELEMENT_LOCK')
		);
	}

	/**
	 * @param array $params
	 * @return array|null
	 */
	protected function actionElementWorkflowStatusPanel(array $params = [])
	{
		if (!Loader::includeModule('workflow'))
			return null;

		$name = (isset($params['NAME']) && $params['NAME'] != ''
			? $params['NAME']
			: Loc::getMessage('IBLOCK_GRID_PANEL_ACTION_ELEMENT_WORKFLOW_STATUS')
		);

		$statusList = [];
		$iterator = \CWorkflowStatus::getDropDownList('N', 'desc');
		while ($row = $iterator->Fetch())
		{
			$statusList[] = [
				'NAME' => $row['REFERENCE'],
				'VALUE' => $row['REFERENCE_ID']
			];
		}
		unset($row, $iterator);
		if (empty($statusList))
			return null;

		$params['APPLY_BUTTON_ID'] = 'send_workflow_status';
		return [
			'name' => $name,
			'type' => 'multicontrol',
			'action' => [
				[
					'ACTION' => Main\Grid\Panel\Actions::RESET_CONTROLS
				],
				[
					'ACTION' => Main\Grid\Panel\Actions::CREATE,
					'DATA' => [
						[
							'TYPE' => Main\Grid\Panel\Types::DROPDOWN,
							'ID' => $this->getElementId('workflow_status'),
							'NAME' => 'wf_status_id',
							'ITEMS' => $statusList
						],
						$this->getApplyButton($params)
					]
				]
			]
		];
	}

	/**
	 * @return array|null
	 */
	protected function actionElementWorkflowStatusRequest()
	{
		$result = $this->request->get('wf_status_id');
		return (is_string($result) ? ['WF_STATUS_ID' => $result] : null);
	}
}