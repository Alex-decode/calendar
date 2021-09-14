<?php

declare(strict_types=1);
/**
 * Calendar App
 *
 * @copyright 2021 Anna Larch <anna.larch@gmx.net>
 *
 * @author Anna Larch <anna.larch@gmx.net>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\Calendar\Controller;

use OCA\Calendar\Exception\ServiceException;
use OCA\Calendar\Http\JsonResponse;
use OCA\Calendar\Service\AppointmentService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUser;

/**
 * Class PublicViewController
 *
 * @package OCA\Calendar\Controller
 */
class AppointmentsController extends Controller {

	/** @var IInitialStateService */
	private $initialStateService;

	/** @var IUser */
	private $user;

	/** @var AppointmentService */
	private $appointmentService;

	/**
	 * @param string $appName
	 * @param IRequest $request an instance of the request
	 * @param IInitialStateService $initialStateService
	 * @param IUser $user
	 * @param AppointmentService $appointmentService
	 */
	public function __construct(string               $appName,
								IRequest             $request,
								IInitialStateService $initialStateService,
								IUser                $user,
								AppointmentService   $appointmentService) {
		parent::__construct($appName, $request);
		$this->initialStateService = $initialStateService;
		$this->user = $user;
		$this->appointmentService = $appointmentService;
	}

	/**
	 * @param string $renderAs
	 * @return TemplateResponse
	 */
	public function index(string $renderAs
							):TemplateResponse {
		$appointments = [];
		try {
			$appointments =  $this->appointmentService->getAllAppointmentConfigurations($this->user->getUID());
		}catch (ServiceException $e) {
			// do nothing and don't show any appointments
		}

		$this->initialStateService->provideInitialState($this->appName, 'appointments', $appointments);

		// show all?
		return new TemplateResponse($this->appName, 'main', [
		], $renderAs);
	}

	/**
	 * @param array $data
	 * @return JsonResponse
	 */
	public function create(array $data): JsonResponse {
		try {
			$appointment = $this->appointmentService->create($data);
			return JsonResponse::success($appointment);
		} catch (ServiceException $e){
			return JsonResponse::errorFromThrowable($e);
		}

	}

	/**
	 * @param int $id
	 * @return JsonResponse
	 */
	public function show(int $id): JsonResponse {
		try {
			$appointment = $this->appointmentService->findById($id);
			return JsonResponse::success($appointment);
		} catch (ServiceException $e){
			return JsonResponse::errorFromThrowable($e);
		}
	}

	/**
	 * @param array $data
	 * @return JsonResponse
	 */
	public function update(array $data): JsonResponse {
		try {
			$appointment = $this->appointmentService->update($data);
			return JsonResponse::success($appointment);
		} catch (ServiceException $e){
			return JsonResponse::errorFromThrowable($e);
		}
	}

	/**
	 * @param int $id
	 * @return JsonResponse
	 */
	public function delete(int $id): JsonResponse {
		try {
			$this->appointmentService->delete($id);
			return JsonResponse::success();
		} catch (ServiceException $e){
			return JsonResponse::errorFromThrowable($e, 403);
		}
	}

	public function cancelSlot($appointment, $slot){
		// apptment id?
		// stub - calDAV
	}
}
