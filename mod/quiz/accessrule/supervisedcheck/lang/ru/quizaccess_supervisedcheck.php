<?php
// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for the quizaccess_supervisedcheck plugin.
 *
 * @package     quizaccess_supervisedcheck
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


$string['allowcontrol']             = 'Позволять попытки только на занятиях?';
$string['checkforall']              = 'Да, для всех типов занятий';
$string['checknotrequired']         = 'Нет';
$string['checkrequired']            = 'Да';
$string['customcheck']              = 'Да, только на занятиях следующих типов:';
$string['iperror']                  = 'Вы не можете выполнять тест. Вы должны находится в классе, где проходит занятие и работать на компьютерах, с которых разрешено тестирование.';
$string['noaccess']                 = 'Вы не можете выполнять тест. Дождитесь необходимого занятия в вашей группе.';
$string['noblockinstance']          = 'Чтобы использовать доступ к тесту только на занятиях, необходимо сначала добавить supervised блок в данный курс.';
$string['pluginname']               = 'Supervised access rule';
$string['uncheckedlessontypes']     = 'Выберите хотя бы один тип занятия';