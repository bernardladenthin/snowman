/**
 * Public domain.
 */
package net.ladenthin.snowman.imager.configuration;

public class Int {
    /**
     * Checks that the specified integer is higher or equals {@code 0}. This
     * method is designed primarily for doing parameter validation in methods
     * and constructors, as demonstrated below:
     * <blockquote><pre>
     * public Foo(int x) {
     *     this.x = Int.requirePositive(x);
     * }
     * </pre></blockquote>
     *
     * @param x the integer to check for negative values
     * @return {@code bar} if not lower {@code 0}
     * @throws NullPointerException if {@code obj} is {@code null}
     */
    public static int requirePositive(int x) {
        if (x < 0)
            throw new RuntimeException();
        return x;
    }
}
