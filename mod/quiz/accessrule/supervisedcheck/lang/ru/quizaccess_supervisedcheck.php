<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for the quizaccess_supervisedcheck plugin.
 *
 * @package   quizaccess_supervisedcheck
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andrey Ushakov <andrey200964@yandex.ru>
 */


defined('MOODLE_INTERNAL') || die();


$string['pluginname'] = 'Supervised access rule';

$string['checknotrequired']         = 'Нет';
$string['checkforall']              = 'Да, для всех типов занятий';
$string['checkrequired']            = 'Да';
$string['customcheck']              = 'Да, только на занятиях следующих типов:';
$string['allowcontrol']             = 'Позволять попытки только на занятиях?';
$string['uncheckedlessontypes']     = 'Выберите хотя бы один тип занятия';
$string['noaccess']                 = 'Вы не можете выполнять тест. Дождитесь необходимого занятия в вашей группе.';
$string['iperror']                  = 'Вы не можете выполнять тест. Вы должны находится в классе, где проходит занятие и работать на компьютерах, с которых разрешено тестирование.';