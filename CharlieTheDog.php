<?php
class CharlieGrid
{
    private $foodLocations;
    private $charlieLocation;
    private $charlieHomeLocation;
    private $numberOfMoves;
    private $strArr;
    private $grid;
    private $flatStrArray;

    public function __construct(array $strArr)
    {
        $this->strArr = $strArr;
        $this->setGrid()
            ->setFlatStrArray()
            ->setFoodLocations()
            ->setCharlieLocation()
            ->setCharlieHomeLocation()
            ->setNumberOfMoves()
        ;
    }

    public function setFlatStrArray(): self
    {
        $string = implode('', $this->strArr);
        $this->flatStrArray = str_split($string);
        return $this;
    }

    public function setGrid(): self
    {
        $this->grid = [
            [0,0], [0,1], [0,2], [0,3],
            [1,0], [1,1], [1,2], [1,3],
            [2,0], [2,1], [2,2], [2,3],
            [3,0], [3,1], [3,2], [3,3],
        ];

        return $this;
    }


    public function setFoodLocations() : self
    {
        $this->foodLocations = [];
        foreach ($this->flatStrArray as $gridIndex => $gridValue) {
            if ($gridValue === 'F') {
                $this->foodLocations[] = $this->grid[$gridIndex];
            }
        }
        return $this;
    }

    public function getFoodLocations(): array
    {
        return $this->foodLocations;
    }

    public function setCharlieLocation(): self
    {
        $this->charlieLocation = $this->grid[array_search('C', $this->flatStrArray)];
        return $this;
    }

    public function getCharlieLocation(): array
    {
        return $this->charlieLocation;
    }

    public function setCharlieHomeLocation(): self
    {
        $this->charlieHomeLocation = $this->grid[array_search('H', $this->flatStrArray)];
        return $this;
    }

    public function getCharlieHomeLocation(): array
    {
        return $this->charlieHomeLocation;
    }

    public function setNumberOfMoves(): self
    {
        $this->numberOfMoves = 0;
        $moves = [];
        foreach($this->getPossibleMoves($this->foodLocations) as $key => $coordinates) {
            $movesToFood = 0;
            array_unshift($coordinates, $this->charlieLocation);
            $coordinates[] = $this->charlieHomeLocation;
            for($i = 0; $i < count($coordinates) - 1; $i++) {
                $movesToFood += $this->getCalculatedCoordinateCombinations($coordinates[$i], $coordinates[$i+1]);
            }
            $moves[] = $movesToFood;
        }
        $this->numberOfMoves = min($moves);
        return $this;
    }

    public function getNumberOfMoves(): int
    {
        return $this->numberOfMoves;
    }

    private function getPossibleMoves(array $foodLocations): Generator
    {
        if (count($foodLocations) <= 1){
            yield $foodLocations;
        } else {
            foreach ($this->getPossibleMoves(array_slice($foodLocations, 1)) as $possibleMove) {
                foreach (range(0, count($foodLocations) - 1) as $index) {
                    yield array_merge(
                        array_slice($possibleMove, 0, $index),
                        [$foodLocations[0]],
                        array_slice($possibleMove, $index)
                    );
                }
            }
        }
    }

    private function getCalculatedCoordinateCombinations($firstCoordinates, $secondCoordinates): int
    {
        $moveFromPositive = $firstCoordinates[0] > $secondCoordinates[0] ? $firstCoordinates[0] - $secondCoordinates[0] : $secondCoordinates[0] - $firstCoordinates[0];
        $moveToPositive = $firstCoordinates[1] > $secondCoordinates[1] ? $firstCoordinates[1] - $secondCoordinates[1] : $secondCoordinates[1] - $firstCoordinates[1];
        return $moveFromPositive + $moveToPositive;
    }
}

function CharlieTheDog($strArr): int
{
    $charlieGrid = new CharlieGrid($strArr);
    return $charlieGrid->getNumberOfMoves();
}


echo CharlieTheDog(['FFOO', 'FFFF', 'CHOO', 'OOOO']);
echo CharlieTheDog(['OFOF', 'OOOO', 'COOH', 'OOOO']);
echo CharlieTheDog(['OOOO', 'OOFF', 'OCHO', 'OFOO']);
echo CharlieTheDog(['OOOO', 'OOOO', 'CHOO', 'OOOO']);
echo CharlieTheDog(['FFOO', 'FFFF', 'CHOO', 'OOOO']);

