<?php
/**
 * @copyright Copyright © 2024 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\Yii\Rbam\Controller;

use BeastBytes\Yii\Http\Response\NotFound;
use BeastBytes\Yii\Http\Response\Redirect;
use BeastBytes\Yii\Rbam\Form\RuleForm;
use BeastBytes\Yii\Rbam\RuleServiceInterface;
use HttpSoft\Message\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Rbac\RuleInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Strings\Inflector;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

use const DIRECTORY_SEPARATOR;

class RuleController
{
    public function __construct(
        private FlashInterface $flash,
        private readonly Inflector $inflector,
        private readonly RuleServiceInterface $ruleService,
        private TranslatorInterface $translator,
        private ViewRenderer $viewRenderer
    )
    {
        $this->translator = $this
            ->translator
            ->withDefaultCategory('rbam')
        ;
        $this->viewRenderer = $this
            ->viewRenderer
            ->withViewPath('@views/rule')
        ;
    }

    public function index(ServerRequest $request): ResponseInterface
    {
        $queryParams = $request
            ->getQueryParams()
        ;
        $rules = $this
            ->ruleService
            ->getRules()
        ;

        return $this
            ->viewRenderer
            ->render(
                'index',
                [
                    'currentPage' => (int) ArrayHelper::getValue($queryParams, 'page', 1),
                    'pageSize' => (int) ArrayHelper::getValue($queryParams, 'pagesize', 20),
                    'rules' => $rules
                ]
            )
        ;
    }

    public function add(
        FormHydrator $formHydrator,
        Redirect $redirect,
        ServerRequestInterface $request,
    ): ResponseInterface
    {
        $formModel = new RuleForm($this->translator);

        if (
            $request->getMethod() === Method::POST
            && $formHydrator->populate($formModel, $request->getParsedBody())
            && $formModel->isValid()
        ) {
            $this
                ->ruleService
                ->save($formModel)
            ;

            return $redirect
                ->toRoute(
                    'rbam.viewRule',
                    [
                        'name' => $this
                            ->inflector
                            ->toSnakeCase($formModel->getName()),
                    ]
                )
                ->withStatusCode(Status::SEE_OTHER)
                ->create()
            ;
        }

        return $this
            ->viewRenderer
            ->render('ruleForm', ['formModel' => $formModel])
        ;
    }

    /**
     * Deletes authorisation rules
     *
     * The IDs of the rules to be deleted are contained in $_POST['ids']
     *
     * @return mixed The return value
    public function delete(): ResponseInterface
    {
        $am = Yii::$app->authManager;
        $success = $notDeleted = $notRemoved = [];

        foreach ($_POST['ids'] as $id) {
            if (!$am->remove($am->getRule($id))) {
                $notRemoved[] = $id;
            } else {
                if (unlink(str_replace('\\', '/', Yii::getAlias('@'.str_replace('\\', '/', $this->module->namespace))) . '/' . $id . '.php')) {
                    $success[] = $id;
                } else {
                    $notDeleted[] = $id;
                }
            }
        }

        if (!empty($success)) {
            Yii::$app->session->setFlash('success', [Yii::t('rbam', 'The following authorisation rules have been removed and their class files deleted: {success}', [
                'success' => Inflector::sentence($success, ' and ', ', and')
            ])]);
        }

        if (!empty($failure)) {
            Yii::$app->session->setFlash('failure', [Yii::t('rbam', 'The following authorisation rules have not been removed: {failure}', [
                'failure' => Inflector::sentence($notRemoved, ' and ', ', and')
            ])]);
        }

        if (!empty($failure)) {
            Yii::$app->session->setFlash('failure', [Yii::t('rbam', 'The following authorisation rules have been removed but their class files have not been deleted: {failure}', [
                'failure' => Inflector::sentence($notDeleted, ' and ', ', and')
            ])]);
        }

        return $this->redirect(['index']);
    }
     */

    /**
     * Update a rule
     *
     * @return mixed The return value
     */
    public function update(
        CurrentRoute $currentRoute,
        FormHydrator $formHydrator,
        NotFound $notFound,
        Redirect $redirect,
        ServerRequestInterface $request,
    ): ResponseInterface
    {
        $name = $this
            ->inflector
            ->toPascalCase($currentRoute->getArgument('name'))
        ;

        $previousName = $name;
        $rule = $this
            ->ruleService
            ->getRule($name)
        ;

        if ($rule === null) {
            return $notFound->create();
        }

        $formModel = new RuleForm($this->translator);
        $formHydrator->populate(model: $formModel, data: $this->getFormData($rule), scope: '');

        if (
            $request->getMethod() === Method::POST
            && $formHydrator->populate($formModel, $request->getParsedBody())
            && $formModel->isValid()
        ) {
            if (
                $this
                    ->ruleService
                    ->save($formModel, $previousName)
            ) {
                return $redirect
                    ->toRoute('rbam.viewRule', ['name' => $formModel->getName()])
                    ->withStatusCode(Status::SEE_OTHER)
                    ->create()
                ;

            }

            // @todo What if rule wasn't saved
        }

        return $this
            ->viewRenderer
            ->render('ruleForm', ['formModel' => $formModel])
        ;
    }

    /**
     * View an authorisation rule
     *
     * @param string $name Rule name
     * @return mixed The return value
     */
    public function view(
        CurrentRoute $currentRoute,
        NotFound $notFound,
        Redirect $redirect
    ): ResponseInterface
    {
        $name = $this
            ->inflector
            ->toPascalCase($currentRoute->getArgument('name'))
        ;

        $rule = $this
            ->ruleService
            ->getRule($name)
        ;

        //$usedBy = Yii::$app->authManager->getItemsByRule($name);
        return $this
            ->viewRenderer
            ->render('view', [
                'rule' => $rule,
            ]);
    }

    private function getFormData(RuleInterface $rule): array
    {
        return [
            'code' => 'return true;',
            'description' => 'Always TRUE rule',
            'name' => 'Demo'
        ];

        /*
        $reflector = new ReflectionClass($rule);
        $executeMethod = $reflector->getMethod('execute');

        $start = $executeMethod->getStartLine() + 1;
        $end = $executeMethod->getEndLine() - 1;

        $rulesDir = str_replace('\\', '/', Yii::getAlias('@'.str_replace('\\', '/', $this->module->rulesNamespace)));

        if (!is_dir($rulesDir)) {
            throw new \Exception('Rules directory does not exist');
        }

        $filename = $rulesDir . '/' . $reflector->getShortName() . '.php';
        $code = array_slice(file($filename), $start, $end - $start);

        foreach ($code as &$line) {
            $line = trim($line);
        }

        return implode("\n", $code);
        */
    }
}
