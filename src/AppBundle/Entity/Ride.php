<?php

namespace AppBundle\Entity;

use AppBundle\DTO\RideDto;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class Ride
 * @package Tests\AppBundle
 *
 * @ORM\Entity()
 * @ORM\Table(name="rides")
 */
class Ride
{
    /**
     * @var AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="passengerId", referencedColumnName="id")
     */
    private $passenger;

    /**
     * @var AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="driverId", referencedColumnName="id")
     */
    private $driver;

    /**
     * @var AppLocation
     * @ORM\ManyToOne(targetEntity="AppLocation", fetch="EAGER")
     * @ORM\JoinColumn(name="departureId", referencedColumnName="id")
     */
    private $departure;

    /**
     * @var AppLocation
     * @ORM\ManyToOne(targetEntity="AppLocation", fetch="EAGER")
     * @ORM\JoinColumn(name="destinationId", referencedColumnName="id")
     */
    private $destination;

    /**
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var \DateTime $created
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * Ride constructor.
     * @param AppUser $passenger
     * @param AppLocation $departure
     */
    public function __construct(AppUser $passenger, AppLocation $departure)
    {
        $this->id = Uuid::uuid4();
        $this->passenger = $passenger;
        $this->departure = $departure;
        $this->created = new \DateTime(null, new \DateTimeZone('UTC'));
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    public function assignDestination(AppLocation $destination)
    {
        $this->destination = $destination;
    }

    public function hasDestination()
    {
        return ! is_null($this->destination);
    }

    public function assignDriver(AppUser $driver)
    {
        $this->driver = $driver;
    }

    public function isDrivenBy(AppUser $driver)
    {
        return $this->driver->is($driver);
    }

    public function hasDriver()
    {
        return ! is_null($this->driver);
    }

    public function isDestinedFor(AppLocation $destinationLocation)
    {
        return $this->destination->isSameAs($destinationLocation);
    }

    public function getPassengerTransaction(RideEventType $status)
    {
        return new RideEvent(
            $this,
            $this->passenger,
            $status
        );
    }

    public function is(Ride $rideToCompare)
    {
        return $this->id->equals($rideToCompare->id);
    }

    public function toDto()
    {
        return new RideDto(
            $this,
            $this->passenger,
            $this->driver,
            $this->destination
        );
    }
}
