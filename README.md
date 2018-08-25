![Security](https://sonarcloud.io/api/project_badges/measure?project=internal-filter&metric=security_rating) ![Readability](https://sonarcloud.io/api/project_badges/measure?project=internal-filter&metric=reliability_rating) ![Mantainability](https://sonarcloud.io/api/project_badges/measure?project=internal-filter&metric=sqale_rating)

# interval-filter

This is a module for the [Drupal](https://www.drupal.org/) 8 CMS that adds a custom filter. It will allow you to customize views with the concept of **interval** (range). An interval is defined by a pair of fields representing the interval bounds.

The type of fields to be used as bounds are intentionally left unrestricted so that it can hopefully work in a wide variety of scenarios. The rule is: if it behaves correctly in the SQL context when applied to operators: `<`, `<=`, `>`, `>=`; then it will work as expected.

## How to use it
1) Download this repository (as ZIP)
2) Install and activate the extension
3) Add a new *Interval Filter* to a view.
![Add Interval Filter](https://user-images.githubusercontent.com/14260975/41561660-cbb45ffa-734a-11e8-8630-3778c7bc5b2e.png)
4) Choose the operator (either *contains* or *not contains*)
5) Choose the fields to be used to be used respectively as MIN (left bound) and MAX (right bound).
![Configure Interval Filter](https://user-images.githubusercontent.com/14260975/41561711-efe0f816-734a-11e8-920c-81fbee20d93b.png)

The filter works as follows: It will check if the choosed value belongs (or not, depending on the operator) to the interval bounded by MIN and MAX. The fields MIN and MAX can also be *null*, this means that the interval is unbounded (either left or right or both).

**Example:** an item has MIN field = 100 and MAX field null. If value ≥ 100 and operator is *contains*, it will show that item.

## SonarQube Analysis
https://sonarcloud.io/dashboard?id=internal-filter
