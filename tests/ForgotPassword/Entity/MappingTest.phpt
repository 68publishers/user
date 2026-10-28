<?php

declare(strict_types=1);

namespace SixtyEightPublishers\User\Tests\ForgotPassword\Entity;

use Tester\Assert;
use ReflectionMethod;
use Doctrine\ORM\Events;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Types\GuidType;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use SixtyEightPublishers\User\ForgotPassword\Entity\UserInterface;
use SixtyEightPublishers\User\ForgotPassword\Entity\PasswordRequest;
use SixtyEightPublishers\DoctrineBridge\Bridge\Nette\DI\EntityMapping;
use SixtyEightPublishers\User\ForgotPassword\DI\ForgotPasswordExtension;
use SixtyEightPublishers\User\Tests\Fixtures\Attribute\User as AttributeUser;
use SixtyEightPublishers\User\Tests\Fixtures\Annotation\User as AnnotationUser;

require __DIR__ . '/../../bootstrap.php';

if (!Type::hasType('uuid')) {
	Type::addType('uuid', GuidType::class);
}

/**
 * The package entities (attributes, registered like doctrine-bridge does) + an application entity mapped by $appDriver.
 */
function createEntityManager(string $appNamespace, string $appUserClass, ?MappingDriver $appDriver = NULL): EntityManager
{
	$chain = new MappingDriverChain();
	$attributeDriver = new AttributeDriver([], TRUE);

	foreach ((new ForgotPasswordExtension())->getEntityMappings() as $mapping) {
		Assert::same(EntityMapping::DRIVER_ATTRIBUTE, $mapping->driver);
		$attributeDriver->addPaths([$mapping->path]);
		$chain->addDriver($attributeDriver, $mapping->namespace);
	}

	$chain->addDriver($appDriver ?? $attributeDriver, $appNamespace);

	$config = new Configuration();
	$config->setMetadataDriverImpl($chain);
	$config->setProxyDir(TEMP_PATH . '/proxies');
	$config->setProxyNamespace('Proxies');
	$config->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER, TRUE)); # like nettrine/orm

	if (PHP_VERSION_ID >= 80400 && method_exists($config, 'enableNativeLazyObjects')) {
		$config->enableNativeLazyObjects(TRUE);
	}

	$resolveTargetEntityListener = new ResolveTargetEntityListener();
	$resolveTargetEntityListener->addResolveTargetEntity(UserInterface::class, $appUserClass, []);
	$eventManager = new EventManager();
	$eventManager->addEventListener([Events::loadClassMetadata, Events::onClassMetadataNotFound], $resolveTargetEntityListener);

	$connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => TRUE], NULL, $eventManager);

	return (new ReflectionMethod(EntityManager::class, '__construct'))->isPublic()
		? new EntityManager($connection, $config, $eventManager)
		: EntityManager::create($connection, $config, $eventManager); # protected constructor in older doctrine/orm 2 versions
}

function assertMapping(EntityManager $em, string $appUserClass): void
{
	$metadata = $em->getClassMetadata(PasswordRequest::class);

	Assert::same('password_request', $metadata->getTableName());
	Assert::same(['id'], $metadata->getIdentifierFieldNames());
	Assert::same(['onPreUpdate'], $metadata->getLifecycleCallbacks(Events::preUpdate));
	Assert::same($appUserClass, $metadata->getAssociationTargetClass('user'));
	Assert::same('reset_user_agent', $metadata->getColumnName('resetDeviceInfo.userAgent'));

	$schemaTool = new SchemaTool($em);
	$schemaTool->createSchema([$em->getClassMetadata($appUserClass), $metadata]);

	$table = $schemaTool->getSchemaFromMetadata([$em->getClassMetadata($appUserClass), $metadata])->getTable('password_request');
	Assert::same(15, $table->getColumn('status')->getLength());
	Assert::same(45, $table->getColumn('request_ip')->getLength());
	Assert::false($table->getColumn('user_id')->getNotnull());
	Assert::same('CASCADE', array_values($table->getForeignKeys())[0]->getOption('onDelete'));

	# persist, update (the PreUpdate lifecycle callback) and load through the resolved target entity
	$user = new $appUserClass('0b6ef2a3-0d2b-4b8e-9d7c-1b2f6b8e2a51', 'john@example.com');
	$request = new PasswordRequest($user);
	$request->getRequestDeviceInfo()->fill();
	$em->persist($user);
	$em->persist($request);
	$em->flush();

	$updated = $request->getUpdated();
	$request->setStatus(PasswordRequest::STATUS_CANCELED);
	$em->flush();
	Assert::notSame($updated, $request->getUpdated());

	$em->clear();
	$loaded = $em->find(PasswordRequest::class, $request->getId());
	Assert::same(PasswordRequest::STATUS_CANCELED, $loaded->getStatus());
	Assert::same('john@example.com', $loaded->getUser()->getEmail());
}

if (class_exists(AnnotationReader::class) && class_exists('Doctrine\ORM\Mapping\Driver\AnnotationDriver')) { # doctrine/orm 2
	test('Attribute-mapped package entities with an annotation-mapped application entity (doctrine/orm 2)', static function (): void {
		$driverClass = 'Doctrine\ORM\Mapping\Driver\AnnotationDriver';
		$annotationDriver = new $driverClass(new AnnotationReader(), [__DIR__ . '/../../Fixtures/Annotation']);
		$em = createEntityManager('SixtyEightPublishers\User\Tests\Fixtures\Annotation', AnnotationUser::class, $annotationDriver);

		assertMapping($em, AnnotationUser::class);
	});
}

test('Attribute-mapped package entities with an attribute-mapped application entity', static function (): void {
	assertMapping(createEntityManager('SixtyEightPublishers\User\Tests\Fixtures\Attribute', AttributeUser::class), AttributeUser::class);
});
