<?php
/**
 * ------------------------------------------------------------------------
 * JA Megafilter Component
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2016 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

abstract class JFormFieldJamegafilter_filterfields extends JFormField {

	protected $type = 'jamegafilter_filterfields';

	protected function getInput() {
		$filterfields = $this->getFilterFields();
		$html = '';
		$html .= '<div id="tabs">';
		$html .= '<ul>';
		foreach ($filterfields as $key => $fieldgroups) {
			if (!empty($fieldgroups)) {
				$html .= '<li><a href="#' . $key . '"><span class="icon-menu"></span>' . JText::_("COM_JAMEGAFILTER_" . strtoupper($key)) . '</a></li>';
			}
		}
		$html .= '</ul>';
		$t = 0;
		$u = 0;
		foreach ($filterfields as $key => $fieldgroups) {
			if (!empty($fieldgroups)) {
				$html .= '<div style="display:none" id="' . $key . '">';
				$html .= '<table class="table table-striped">';
				$html .= '<thead>
									<tr>
										<th><span class="icon-menu-2"></span></th>
										<th>' . JText::_('COM_JAMEGAFILTER_PUBLISHED') . '</th>
										<th>' . JText::_('COM_JAMEGAFILTER_SORT_BY') . '</th>
										<th>' . JText::_('COM_JAMEGAFILTER_NAME') . '</th>
										<th>' . JText::_('COM_JAMEGAFILTER_TITLE') . '</th>
										<th>' . JText::_('COM_JAMEGAFILTER_FILTER_TYPE') . '</th>
									</tr>
								</thead>';
				$html .= '<tbody>';
				foreach ($fieldgroups as $field) {
					$html .= '<tr>';
					$html .= '<td><span class="icon-menu large-icon"> </span></td>';
					$html .= '<td>';
					$publish = $field['published'] ? 'publish' : 'unpublish';
					$html .= '<a class="btn btn-micro" input="" href="javascript:void(0);" onclick="return publish_item(this)"><span class="icon-' . $publish . '"></span></a>';
					$html .= '<input type="hidden" name="jform[filterfields][' . $key . '][' . $t . '][published]" value="' . $field['published'] . '">';
					$html .= '</td>';

					$html .= '<td>';
					if (strpos($field['field'], '.')) {
						$field['sort'] = 0;
						$html .= '<a class="btn btn-micro" input="" href="javascript:void(0);" disabled><span class="icon-delete"></span></a>';
					} else {
						$sort = $field['sort'] ? 'publish' : 'unpublish';
						$html .= '<a class="btn btn-micro" input="" href="javascript:void(0);" onclick="return publish_item(this)"><span class="icon-' . $sort . '"></span></a>';
					}
					$html .= '<input type="hidden" name="jform[filterfields][' . $key . '][' . $t . '][sort]" value="' . $field['sort'] . '">';
					$html .= '</td>';

					$html .= '<input type="hidden" name="jform[filterfields][' . $key . '][' . $t . '][field]" value="' . $field['field'] . '">';

					$html .= '<td><label for="filterfield_' . $key . '_' . str_replace('.', '_', $field['field']) . '" >' . $field['name'] . '</label</td>';

					$html .= '<td><input id="filterfield_' . $key . '_' . str_replace('.', '_', $field['field']) . '" class="inputbox" name="jform[filterfields][' . $key . '][' . $t . '][title]" type="text" value="' . $field['title'] . '" required></td>';
					$html .= '<td>';
					$html .= '<select name="jform[filterfields][' . $key . '][' . $t . '][type]">';

					foreach ($field['filter_type'] as $type) {
						$selectd = !empty($field['type']) && $field['type'] === $type ? 'selected' : '';
						$html .= '<option value="' . $type . '" ' . $selectd . '>' . ucfirst($type) . '</option>';
					}

					$html .= '</select>';
					$html .= '</td>';

					$html .= '</tr>';
					$t++;
				}

				$html .= '</tbody>';
				$html .= '</table>';
				$html .= '</div>';
				$u++;
			}
		}

		$html .= '</div>';

		return $html;
	}

	function getFilterFields() {
		$fieldgroups = $this->getFieldGroups();

		$filterfields = array();

		if (empty($this->value)) {

			$filterfields = $fieldgroups;
		} else {

			foreach ($this->value as $key => $group_value) {

				if (!empty($fieldgroups[$key])) {

					$filterfields[$key] = array();
					foreach ($group_value as $gv) {

						foreach ($fieldgroups[$key] as $k_fg => $fg) {

							if ($gv['field'] == $fg['field']) {

								$field = $gv + $fg;
								array_push($filterfields[$key], $field);

								unset($fieldgroups[$key][$k_fg]);

								break;
							}
						}
					}

					$filterfields[$key] = array_merge($filterfields[$key], $fieldgroups[$key]);

					// unset the same field group
					unset($fieldgroups[$key]);
				}
			}

			$filterfields = array_merge($filterfields, $fieldgroups);
		}

		return $filterfields;
	}

	/**
	 *  @return array
	 */
	abstract function getFieldGroups();
}
